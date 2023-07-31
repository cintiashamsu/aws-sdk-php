<?php
namespace Aws\S3;

use Aws\CommandInterface;
use Aws\Multipart\DownloadState;
use Aws\ResultInterface;
use GuzzleHttp\Psr7;

trait MultipartDownloadingTrait
{
    private $downloadedBytes = 0;

    /**
     * Creates a DownloadState object for a multipart download by querying the
     * service for the specified download's information.
     *
     * @param S3ClientInterface $client   S3Client used for the download.
     * @param string            $bucket   Bucket for the multipart download.
     * @param string            $key      Object key for the multipart download.
     *
     * @return DownloadState
     */
    public static function getStateFromService(
        S3ClientInterface $client,
                          $bucket,
                          $key,
                          $dest
    ) {
        $state = new DownloadState([
            'Bucket'   => $bucket,
            'Key'      => $key
        ]);

        $info = $client->headObject([
            'Bucket'   => $bucket,
            'Key'      => $key,
//            'ChecksumMode' => 'ENABLED'
        ]);

        $totalSize = $info['ContentLength'];
        $state->setPartSize(1048576);
        $partSize = $state->getPartSize();
        $destStream = new Psr7\LazyOpenStream($dest, 'rw');

        for ($byte = 0; $byte <= $totalSize; $byte+=$partSize) {
            $stream = new Psr7\LimitStream($destStream, $partSize, $byte);
            echo $stream->getSize() . "\n";
            echo $stream->tell() . "\n";
            // mark part as downloaded as you check
        }

        $state->setStatus(DownloadState::INITIATED);
        return $state;
    }

    protected function handleResult($command, ResultInterface $result)
    {
        if ($this->checksumMode) {
            if ($this->validateChecksum($result)){
                throw new \Exception('Checksum invalid.');
            }
        }

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
        if ($this->getState()->displayProgress) {
            $this->downloadedBytes+=strlen($result['Body']);
            $this->getState()->getDisplayProgress($this->downloadedBytes);
        }
    }

    private function validateChecksum($result)
    {
        if (isset($result['ChecksumValidated'])) {
            $checksum = CalculatesChecksumTrait::getEncodedValue(
                $result['ChecksumValidated'], $result['Body']
            );
            if ($checksum != $result['Checksum' . $result['ChecksumValidated']]) {
                return true;
            }
        } elseif (!strpos($result['ETag'], '-')
            && $result['ETag'] != md5($result['Body'])) {
            return true;
        }
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

        if (isset($this->checksumMode)) {
            $params['ChecksumMode'] = 'ENABLED';
        }

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
