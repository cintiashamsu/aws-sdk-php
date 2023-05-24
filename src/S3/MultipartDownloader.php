<?php
namespace Aws\S3;

use Aws\Arn\ArnParser;
use Aws\HashingStream;
use Aws\Multipart\AbstractDownloader;
use Aws\PhpHash;
use Aws\ResultInterface;
use GuzzleHttp\Psr7;
use Psr\Http\Message\StreamInterface as Stream;
use Aws\S3\Exception\S3MultipartUploadException;

/**
 * Encapsulates the execution of a multipart download to S3.
 */
class MultipartDownloader extends AbstractDownloader
{
    use MultipartDownloadingTrait;

    const PART_MIN_SIZE = 5242880;
    const PART_MAX_SIZE = 5368709120;
    const PART_MAX_NUM = 10000;

    public function __construct(
        S3ClientInterface $client,
                          $source,
        array $config = []
    ) {
        $result = $this->getObjectInfo($client, $config['bucket'], $config['key']);
        echo $result['ContentLength'];
        parent::__construct($client, $source, array_change_key_case($config) + [
                'bucket' => null,
                'key'    => null,
                'exception_class' => S3MultipartUploadException::class,
            ]);
        if (isset($config['track_upload']) && $config['track_upload']) {
            $this->getState()->setProgressThresholds($this->source->getSize());
        }
    }

    public function getObjectInfo($client, $bucket, $key)
    {
        return $client->headObject([
            'Bucket' => $bucket,
            'Key' => $key,
        ]);
    }

    protected function loadDownloadWorkflowInfo()
    {
        return [
            'command' => [
                'initiate' => 'getObject',
                'download'   => 'getObject',
                'complete' => 'getObject',
            ],
            'id' => [
                'bucket'    => 'Bucket',
                'key'       => 'Key',
                'download_id' => 'DownloadId',
            ],
            'part_num' => 'PartNumber',
        ];
    }

    protected function createPart($seekable, $number)
    {
        // Initialize the array of part data that will be returned.
        $data = [];

        // Apply custom params to DownloadPart data
        $config = $this->getConfig();
        $params = isset($config['params']) ? $config['params'] : [];
        foreach ($params as $k => $v) {
            $data[$k] = $v;
        }

        $data['PartNumber'] = $number;

        // Read from the source to create the body stream.
        if ($seekable) {
            // Case 1: Source is seekable, use lazy stream to defer work.
            $body = $this->limitPartStream(
                new Psr7\LazyOpenStream($this->source->getMetadata('uri'), 'r')
            );
        } else {
            // Case 2: Stream is not seekable; must store in temp stream.
            $source = $this->limitPartStream($this->source);
            $source = $this->decorateWithHashes($source, $data);
            $body = Psr7\Utils::streamFor();
            Psr7\Utils::copyToStream($source, $body);
        }

        $contentLength = $body->getSize();

        // Do not create a part if the body size is zero.
        if ($contentLength === 0) {
            return false;
        }

        $body->seek(0);
        $data['Body'] = $body;

        if (isset($config['add_content_md5'])
            && $config['add_content_md5'] === true
        ) {
            $data['AddContentMD5'] = true;
        }

        $data['ContentLength'] = $contentLength;

        return $data;
    }

    protected function extractETag(ResultInterface $result)
    {
        return $result['ETag'];
    }

    protected function getSourceMimeType()
    {
        if ($uri = $this->source->getMetadata('uri')) {
            return Psr7\MimeType::fromFilename($uri)
                ?: 'application/octet-stream';
        }
    }

    protected function getSourceSize()
    {
        return $this->source->getSize();
    }

    /**
     * Decorates a stream with a sha256 linear hashing stream.
     *
     * @param Stream $stream Stream to decorate.
     * @param array  $data   Part data to augment with the hash result.
     *
     * @return Stream
     */
    private function decorateWithHashes(Stream $stream, array &$data)
    {
        // Decorate source with a hashing stream
        $hash = new PhpHash('sha256');
        return new HashingStream($stream, $hash, function ($result) use (&$data) {
            $data['ContentSHA256'] = bin2hex($result);
        });
    }
}