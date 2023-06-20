<?php
namespace Aws\S3;

use Aws\CommandInterface;
use Aws\Multipart\DownloadState;
use Aws\ResultInterface;
use GuzzleHttp\Psr7;

trait MultipartDownloadingTrait
{
    /**
     * Creates a DownloadState object for a multipart download by querying the
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

    protected function writeDestStream($position, $body)
    {
        $this->destStream->seek($position);
        $this->destStream->write($body->getContents());
    }

    abstract protected function extractETag(ResultInterface $result);

    protected function determinePartSize()
    {
        // Make sure the part size is set.
        $partSize = $this->getConfig()['part_size'] ?: MultipartDownloader::PART_MIN_SIZE;

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
        $params = $config['params'] ?? [];

        if (isset($config['acl'])) {
            $params['ACL'] = $config['acl'];
        }

        $params[$configType['config']] = $configType['configParam'];

        return $params;
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

    /**
     * @return DownloadState
     */
    abstract protected function getState();

    /**
     * @return array
     */
    abstract protected function getConfig();
}
