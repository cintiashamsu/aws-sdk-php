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

    protected function handleResult(CommandInterface $command, ResultInterface $result)
    {
        $this->getState()->markPartAsUploaded($command['PartNumber'], [
            'PartNumber' => $command['PartNumber'],
            'ETag'       => $this->extractETag($result),
        ]);

        $this->writeDestStream($command['PartNumber'], $result['Body']);
    }

    protected function writeDestStream($partNum, $body)
    {
        $bodyStream = Psr7\Utils::streamFor($body);
        $this->destStream->seek($this->streamPositionArray[$partNum]);
        $this->destStream->write($bodyStream->read(5242880));
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

    protected function getInitiateParams()
    {
        $config = $this->getConfig();
        $params = isset($config['params']) ? $config['params'] : [];

        if (isset($config['acl'])) {
            $params['ACL'] = $config['acl'];
        }

        if (isset($config['partnumber'])) {
            $params['PartNumber'] = $config['partnumber'];
            echo 'PartNumber';
        } elseif (isset($config['range'])) {
            $params['Range'] = $config['range'];
            echo 'Range';
        } elseif (isset($config['multipartdownloadtype']) && $config['multipartdownloadtype'] == 'Range') {
            $params['Range'] = 'bytes=0-'.MultipartDownloader::PART_MIN_SIZE;
            echo 'multipartdownloadtype';
        } else {
            $params['PartNumber'] = 1;
            echo 'part num 1';
        }

//        $params['PartNumber'] = $config['partnumber'];

//        $params['Range'] = 'bytes=0-1225';

        return $params;
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
