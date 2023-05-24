<?php
namespace Aws\S3;

use Aws\CommandInterface;
use Aws\Multipart\DownloadState;
use Aws\ResultInterface;

trait MultipartDownloadingTrait
{
    private $downloadedBytes = 0;

    /**
     * Creates an DownloadState object for a multipart download by querying the
     * service for the specified download's information.
     *
     * @param S3ClientInterface $client   S3Client used for the download.
     * @param string            $bucket   Bucket for the multipart download.
     * @param string            $key      Object key for the multipart download.
     * @param string            $downloadId Download ID for the multipart download.
     *
     * @return DownloadState
     */
    public static function getStateFromService(
        S3ClientInterface $client,
                          $bucket,
                          $key,
                          $downloadId
    ) {
        $state = new DownloadState([
            'Bucket'   => $bucket,
            'Key'      => $key,
            'DownloadId' => $downloadId,
        ]);

        foreach ($client->getPaginator('ListParts', $state->getId()) as $result) {
            // Get the part size from the first part in the first result.
            if (!$state->getPartSize()) {
                $state->setPartSize($result->search('Parts[0].Size'));
            }
            // Mark all the parts returned by ListParts as downloaded.
            foreach ($result['Parts'] as $part) {
                $state->markPartAsDownloaded($part['PartNumber'], [
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
        $this->getState()->markPartAsDownloaded($command['PartNumber'], [
            'PartNumber' => $command['PartNumber'],
            'ETag'       => $this->extractETag($result),
        ]);

        $this->downloadedBytes += $command["ContentLength"];
        $this->getState()->displayProgress($this->downloadedBytes);
    }

    abstract protected function extractETag(ResultInterface $result);

    protected function getCompleteParams()
    {
        $config = $this->getConfig();
        $params = isset($config['params']) ? $config['params'] : [];

        $params['MultipartDownload'] = [
            'Parts' => $this->getState()->getDownloadedParts()
        ];

        return $params;
    }

    protected function determinePartSize()
    {
        // Make sure the part size is set.
        $partSize = $this->getConfig()['part_size'] ?: MultipartDownloader::PART_MIN_SIZE;

        // Adjust the part size to be larger for known, x-large downloads.
        if ($sourceSize = $this->getSourceSize()) {
            $partSize = (int) max(
                $partSize,
                ceil($sourceSize / MultipartDownloader::PART_MAX_NUM)
            );
        }

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

        // Set the ContentType if not already present
        if (empty($params['ContentType']) && $type = $this->getSourceMimeType()) {
            $params['ContentType'] = $type;
        }

        return $params;
    }

    /**
     * @return DownloadState
     */
    abstract protected function getState();

    /**
     * @return array
     */
    abstract protected function getConfig();

    /**
     * @return int
     */
    abstract protected function getSourceSize();

    /**
     * @return string|null
     */
    abstract protected function getSourceMimeType();
}