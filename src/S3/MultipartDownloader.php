<?php
namespace Aws\S3;

use Aws\HashingStream;
use Aws\Multipart\AbstractDownloader;
use Aws\PhpHash;
use Aws\ResultInterface;
use GuzzleHttp\Psr7;
use Psr\Http\Message\StreamInterface as Stream;
use Aws\S3\Exception\S3MultipartDownloadException;

/**
 * Encapsulates the execution of a multipart download to S3.
 */
class MultipartDownloader extends AbstractDownloader
{
    use MultipartDownloadingTrait;

    const PART_MIN_SIZE = 5242880;
    const PART_MAX_SIZE = 5368709120;
    const PART_MAX_NUM = 10000;

    public $destStream;
    public $streamPositionArray;

    /**
     * Creates a multipart download for an S3 object.
     *
     * The valid configuration options are as follows:
     *
     * - acl: (string) ACL to set on the object being download. Objects are
     *   private by default.
     * - before_complete: (callable) Callback to invoke before the
     *   `CompleteMultipartUpload` operation. The callback should have a
     *   function signature like `function (Aws\Command $command) {...}`.
     * - before_initiate: (callable) Callback to invoke before the
     *   `CreateMultipartUpload` operation. The callback should have a function
     *   signature like `function (Aws\Command $command) {...}`.
     * - before_upload: (callable) Callback to invoke before any `UploadPart`
     *   operations. The callback should have a function signature like
     *   `function (Aws\Command $command) {...}`.
     * - bucket: (string, required) Name of the bucket to which the object is
     *   being uploaded, or an S3 access point ARN.
     * - concurrency: (int, default=int(5)) Maximum number of concurrent
     *   `UploadPart` operations allowed during the multipart upload.
     * - key: (string, required) Key to use for the object being uploaded.
     * - params: (array) An array of key/value parameters that will be applied
     *   to each of the sub-commands run by the uploader as a base.
     *   Auto-calculated options will override these parameters. If you need
     *   more granularity over parameters to each sub-command, use the before_*
     *   options detailed above to update the commands directly.
     * - part_size: (int, default=int(5242880)) Part size, in bytes, to use when
     *   doing a multipart upload. This must between 5 MB and 5 GB, inclusive.
     * - prepare_data_source: (callable) Callback to invoke before starting the
     *   multipart upload workflow. The callback should have a function
     *   signature like `function () {...}`.
     * - state: (Aws\Multipart\UploadState) An object that represents the state
     *   of the multipart upload and that is used to resume a previous upload.
     *   When this option is provided, the `bucket`, `key`, and `part_size`
     *   options are ignored.
     *
     * @param S3ClientInterface $client Client used for the upload.
     * @param mixed             $dest Destination for data to download.
     * @param array             $config Configuration used to perform the upload.
     */
    public function __construct(
        S3ClientInterface $client,
        $dest,
        array $config = []
    ) {
        $this->destStream = $this->createDestStream($dest);
        parent::__construct($client, $dest, array_change_key_case($config) + [
                'bucket' => null,
                'key'    => null,
                'exception_class' => S3MultipartDownloadException::class,
            ]);
    }

    protected function loadUploadWorkflowInfo()
    {
        return [
            'command' => [
                'initiate' => 'GetObject',
                'upload'   => 'GetObject',
//                'complete' => 'CompleteMultipartUpload',
            ],
            'id' => [
                'bucket'    => 'Bucket',
                'key'       => 'Key',
                'upload_id' => 'UploadId',
            ],
            'part_num' => 'PartNumber',
        ];
    }

    protected function createPart($partStartPos, $number)
    {
        // Initialize the array of part data that will be returned.
        $data = [];

        // Apply custom params to UploadPart data
        $config = $this->getConfig();
        $params = isset($config['params']) ? $config['params'] : [];
        foreach ($params as $k => $v) {
            $data[$k] = $v;
        }

        // Set Range or PartNumber params
        if (isset($this->config['range']) or
            isset($this->config['multipartdownloadtype'])
            && $this->config['multipartdownloadtype'] == 'Range'){
            $partEndPos = $partStartPos+self::PART_MIN_SIZE;
            $data['Range'] = 'bytes='.$partStartPos.'-'.$partEndPos;
        } else {
            $data['PartNumber'] = $number;
        }

        if (isset($config['add_content_md5'])
            && $config['add_content_md5'] === true
        ) {
            $data['AddContentMD5'] = true;
        }

        return $data;
    }

    protected function extractETag(ResultInterface $result)
    {
        return $result['ETag'];
    }

    public function setStreamPositionArray($sourceSize)
    {
        $parts = ceil($sourceSize/$this->state->getPartSize());
        $position = 0;
        if (isset($this->config['range']) or
            (isset($this->config['multipartdownloadtype']) &&
                $this->config['multipartdownloadtype'] == 'Range')) {
            for ($i = 1; $i <= $parts; $i++) {
                $this->streamPositionArray [$position] = $i;
                $position += $this->state->getPartSize();
            }
        } else {
            for ($i = 1; $i <= $parts; $i++) {
                $this->streamPositionArray [$i] = $position;
                $position += $this->state->getPartSize();
            }
        }
    }

    protected function createDestStream($filePath)
    {
        return new Psr7\LazyOpenStream($filePath, 'w');
    }
}
