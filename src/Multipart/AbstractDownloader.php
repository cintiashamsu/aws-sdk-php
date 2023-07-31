<?php
namespace Aws\Multipart;

use Aws\AwsClientInterface as Client;
use Aws\Exception\AwsException;
use GuzzleHttp\Psr7;
use InvalidArgumentException as IAE;
use Psr\Http\Message\StreamInterface as Stream;

abstract class AbstractDownloader extends AbstractDownloadManager
{
    /** @var Stream Source of the data to be downloaded. */
    protected $source;

    /** @var Numeric Current position to track beginning of part. */
    protected $partPosition = 0;

    /** @var int Size of source. */
    protected $sourceSize;

    /**
     * @param Client $client
     * @param mixed  $dest
     * @param array  $config
     */
    public function __construct(Client $client, $dest, array $config = [])
    {
        $this->config = $config;
        parent::__construct($client, $config);
    }

    protected function getDownloadCommands(callable $resultHandler)
    {
        // Determine if the source can be seeked.
        for ($partNumber = 1; $this->isEof($this->partPosition); $partNumber++) {
            // If we haven't already downloaded this part, yield a new part.
            if (!$this->state->hasPartBeenDownloaded($partNumber)) {
                    $partStartPosition = $this->partPosition;
                    if (!($data = $this->createPart($partStartPosition, $partNumber))) {
                        break;
                    }
                    $command = $this->client->getCommand(
                        $this->info['command']['download'],
                        $data + $this->state->getId()
                    );
                    $command->getHandlerList()->appendSign($resultHandler, 'mup');
                    $numberOfParts = ($this->getNumberOfParts($this->state->getPartSize()));
                    if (isset($numberOfParts) && $partNumber > $numberOfParts) {
                        throw new $this->config['exception_class'](
                            $this->state,
                            new AwsException(
                                "Maximum part number for this job exceeded, file has likely been corrupted." .
                                "  Please restart this upload.",
                                $command
                            )
                        );
                    }
                    yield $command;
                }
                // Advance the source's offset if not already advanced.
                $this->partPosition += $this->state->getPartSize();
            }
    }

    /**
     * Generates the parameters for a download part by analyzing a range of the
     * source starting from the current offset up to the part size.
     *
     * @param numeric $partStartPosition
     * @param int  $partNumber
     *
     * @return array|null
     */
    abstract protected function createPart($partStartPosition, $partNumber);

    /**
     * Checks if the source is at EOF.
     *
     * @param numeric $position
     *
     * @return bool
     */
    private function isEof($position)
    {
        return $position <= $this->sourceSize;
    }

    /**
     * Determines and sets size of the source.
     *
     * @param mixed $range
     *
     */
    protected function determineSourceSize($range)
    {
        $size = substr($range, strpos($range, "/") + 1);
        $this->sourceSize = $size;
    }

    /**
     * Determines and sets number of parts.
     *
     * @param numeric $partSize
     *
     * @return float|null
     */
    protected function getNumberOfParts($partSize)
    {
        if ($this->sourceSize) {
            return ceil($this->sourceSize/$partSize);
        }
        return null;
    }
}