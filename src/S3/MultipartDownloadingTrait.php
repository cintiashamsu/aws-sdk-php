<?php
namespace Aws\S3;

use Aws\CommandInterface;
use Aws\Multipart\DownloadState;
use Aws\ResultInterface;
use GuzzleHttp\Psr7;

trait MultipartDownloadingTrait
{
    /**
     * Creates an UploadState object for a multipart upload by querying the
     * service for the specified upload's information.
     *
     * @param S3ClientInterface $client   S3Client used for the upload.
     * @param string            $bucket   Bucket for the multipart upload.
     * @param string            $key      Object key for the multipart upload.
     * @param string            $uploadId Upload ID for the multipart upload.
     *
     * @return DownloadState
     */
    public static function getStateFromService(
        S3ClientInterface $client,
                          $bucket,
                          $key,
                          $uploadId
    ) {
        $state = new DownloadState([
            'Bucket'   => $bucket,
            'Key'      => $key,
            'UploadId' => $uploadId,
        ]);

        foreach ($client->getPaginator('ListParts', $state->getId()) as $result) {
            // Get the part size from the first part in the first result.
            if (!$state->getPartSize()) {
                $state->setPartSize($result->search('Parts[0].Size'));
            }
            // Mark all the parts returned by ListParts as uploaded.
            foreach ($result['Parts'] as $part) {
                $state->markPartAsUploaded($part['PartNumber'], [
                    'PartNumber' => $part['PartNumber'],
                    'ETag'       => $part['ETag']
                ]);
            }
        }

        $state->setStatus(DownloadState::INITIATED);

        return $state;
    }

    protected function handleResult($command, ResultInterface $result)
    {
        if (!($command instanceof CommandInterface)){
            // single downloads - part/range
            $this->getState()->markPartAsUploaded(1, [
                'PartNumber' => 1,
                'ETag' => $this->extractETag($result),
            ]);
            $this->writeDestStream(0, $result['Body']);
        } elseif (!(isset($command['PartNumber']))) {
            // multi downloads - range
            $seek = substr($command['Range'], strpos($command['Range'], "=") + 1);
            $seek = (int)(strtok($seek, '-'));
            $this->getState()->markPartAsUploaded($this->streamPositionArray[$seek], [
                'PartNumber' => $this->streamPositionArray[$seek],
                'ETag' => $this->extractETag($result),
            ]);
            $this->writeDestStream($seek, $result['Body']);
        } else {
            // multi downloads - part
            $this->getState()->markPartAsUploaded($command['PartNumber'], [
                'PartNumber' => $command['PartNumber'],
                'ETag' => $this->extractETag($result),
            ]);
            $this->writeDestStream($this->streamPositionArray[$command['PartNumber']], $result['Body']);
        }
    }

    protected function writeDestStream($partNum, $body)
    {
        $this->destStream->seek($partNum);
        $this->destStream->write($body->getContents());
    }

    abstract protected function extractETag(ResultInterface $result);

    protected function getCompleteParams()
    {
        $config = $this->getConfig();
        $params = isset($config['params']) ? $config['params'] : [];

        $params['MultipartUpload'] = [
            'Parts' => $this->getState()->getUploadedParts()
        ];

        return $params;
    }

    protected function determinePartSize()
    {
        // Make sure the part size is set.
        $partSize = $this->getConfig()['part_size'] ?: MultipartDownloader::PART_MIN_SIZE;

        // Adjust the part size to be larger for known, x-large uploads.
//        if ($sourceSize = $this->getSourceSize()) {
//            $partSize = (int) max(
//                $partSize,
//                ceil($sourceSize / MultipartDownloader::PART_MAX_NUM)
//            );
//        }

        // Ensure that the part size follows the rules: 5 MB <= size <= 5 GB.
        if ($partSize < MultipartDownloader::PART_MIN_SIZE || $partSize > MultipartDownloader::PART_MAX_SIZE) {
            throw new \InvalidArgumentException('The part size must be no less '
                . 'than 5 MB and no greater than 5 GB.');
        }

        return $partSize;
    }

    protected function getInitiateParams($configType)
    {
        $config = $this->getConfig();
        $params = isset($config['params']) ? $config['params'] : [];

        if (isset($config['acl'])) {
            $params['ACL'] = $config['acl'];
        }

        $params[$configType['config']] = $configType['configParam'];

        return $params;
    }

    protected function getUploadType()
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
                    'type' => 'multi'
            ];
        } else {
            return ['config' => 'PartNumber',
                    'configParam' => 1,
                    'type' => 'multi'];
        }
    }

//    public function setStreamPosArray($sourceSize)
//    {
//        $parts = ceil($sourceSize/$this->partSize);
//        $position = 0;
//        for ($i=1;$i<=$parts;$i++) {
//            $this->StreamPosArray []= $position;
//            $position += $this->partSize;
//        }
//        print_r($this->streamPositionArray);
//    }

    /**
     * @return UploadState
     */
    abstract protected function getState();

    /**
     * @return array
     */
    abstract protected function getConfig();
}
