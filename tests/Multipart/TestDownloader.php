<?php
namespace Aws\Test\Multipart;

use Aws\CommandInterface;
use Aws\Multipart\AbstractDownloader;
use Aws\ResultInterface;
use GuzzleHttp\Psr7;
use Aws\S3\Exception\S3MultipartDownloadException;

/**
 * Concrete UploadBuilder for the purposes of the following test.
 */
class TestDownloader extends AbstractDownloader
{
    public function __construct($client, $source, array $config = [])
    {
        $this->destStream = new Psr7\LazyOpenStream($source, 'w');
        parent::__construct($client, $source, $config + [
                'bucket' => null,
                'key'    => null,
                'exception_class' => S3MultipartDownloadException::class,
            ]);
    }
    protected function loadDownloadWorkflowInfo()
    {
        return [
            'command' => [
                'initiate' => 'GetObject',
                'download'   => 'GetObject'
            ],
            'id' => [
                'bucket'    => 'Bucket',
                'key'       => 'Key',
                'download_id' => 'DownloadId',
            ],
            'part_num' => 'PartNumber',
        ];
    }

    protected function determinePartSize()
    {
        return $this->config['part_size'] ?: 2;
    }

    protected function getInitiateParams($type)
    {
        return [];
    }

    protected function createPart($seekable, $number)
    {
        if ($seekable) {
            $body = Psr7\Utils::streamFor(fopen($this->source->getMetadata('uri'), 'r'));
            $body = $this->limitPartStream($body);
        } else {
            $body = Psr7\Utils::streamFor($this->source->read($this->state->getPartSize()));
        }

        // Do not create a part if the body size is zero.
        if ($body->getSize() === 0) {
            return false;
        }

        return [
            'PartNumber' => $number,
            'Body'       => $body,
            'UploadId' => 'baz'
        ];
    }

    protected function handleResult($command, ResultInterface $result)
    {
        if (!($command instanceof CommandInterface)){
            // single part downloads - part and range
            $partNumber = 1;
            $position = 0;
        } elseif (!(isset($command['PartNumber']))) {
            // multipart downloads - range
            $seek = substr($command['Range'], strpos($command['Range'], "=") + 1);
            $seek = (int)(strtok($seek, '-'));
            $partNumber = $this->streamPositionArray[$seek];
            $position = $seek;
        } else {
            // multipart downloads - part
            $partNumber = $command['PartNumber'];
            $position = $this->streamPositionArray[$command['PartNumber']];
        }

        $this->getState()->markPartAsDownloaded($partNumber, [
            'PartNumber' => $partNumber,
            'ETag' => $this->extractETag($result),
        ]);
        $this->writeDestStream($position, $result['Body']);
    }

    protected function extractETag(ResultInterface $result)
    {
        return $result['ETag'];
    }

    protected function writeDestStream($position, $body)
    {
        $this->destStream->seek($position);
        if ($body) {
            $this->destStream->write($body->getContents());
        }
    }

    protected function getDownloadType()
    {
        $config = $this->getConfig();
        if (isset($config['partnumber'])) {
            return ['config' => 'PartNumber',
                'configParam' => $config['partnumber']];
        } elseif (isset($config['range'])) {
            return ['config' => 'Range',
                'configParam' => $config['range']];
        } elseif (isset($config['multipartdownloadtype']) && $config['multipartdownloadtype'] == 'Range') {
            return ['config' => 'Range',
                'configParam' => 'bytes=0-'.MultipartDownloader::PART_MIN_SIZE,
                'multipart' => 'yes'
            ];
        } else {
            return ['config' => 'PartNumber',
                'configParam' => 1,
                'multipart' => 'yes'];
        }
    }

    public function setStreamPositionArray()
    {
        $parts = ceil($this->sourceSize/$this->state->getPartSize());
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

}
