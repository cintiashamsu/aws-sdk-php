<?php
namespace Aws\Multipart;

use Aws\AwsClientInterface as Client;
use Aws\Exception\AwsException;
use GuzzleHttp\Psr7;
use InvalidArgumentException as IAE;
use Psr\Http\Message\StreamInterface as Stream;

abstract class AbstractDownloader extends AbstractDownloadManager
{
    /** @var Stream Source of the data to be uploaded. */
    protected $source;

    protected $position = 0;

    /**
     * @param Client $client
     * @param mixed  $source
     * @param array  $config
     */
    public function __construct(Client $client, $source, array $config = [])
    {
//        $this->source = $this->determineSource($source);
        $this->config = $config;
        parent::__construct($client, $config);
    }

    protected function getUploadCommands(callable $resultHandler)
    {
        // partnumber - single object calls
        if (isset($this->config['partnumber'])) {
            $data = $this->createPart('partNumber', $this->config['partnumber']);
            $command = $this->client->getCommand(
                $this->info['command']['upload'],
                $data + $this->state->getId()
            );
            $command->getHandlerList()->appendSign($resultHandler, 'mup');
            yield $command;
        } elseif (isset($this->config['range'])){ // range - single object calls
            $data = $this->createPart($this->position, $this->config['partnumber']);
            $command = $this->client->getCommand(
                $this->info['command']['upload'],
                $data + $this->state->getId()

            );
            $command->getHandlerList()->appendSign($resultHandler, 'mup');
            yield $command;
        } else { // multipart calls
            // Determine if the source can be seeked.
            for ($partNumber = 1; $this->isEof($this->position); $partNumber++) {
                // If we haven't already uploaded this part, yield a new part.
                if (!$this->state->hasPartBeenUploaded($partNumber)) {
                    $partStartPos = $this->position;
                    if (!($data = $this->createPart($partStartPos, $partNumber))) {
                        break;
                    }
                    $command = $this->client->getCommand(
                        $this->info['command']['upload'],
                        $data + $this->state->getId()
                    );
                    $command->getHandlerList()->appendSign($resultHandler, 'mup');
                    $numberOfParts = $this->getNumberOfParts($this->state->getPartSize());
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
                $this->position += $this->state->getPartSize();
            }
        }
    }

    /**
     * Generates the parameters for an upload part by analyzing a range of the
     * source starting from the current offset up to the part size.
     *
     * @param bool $seekable
     * @param int  $number
     *
     * @return array|null
     */
    abstract protected function createPart($seekable, $number);

    /**
     * Checks if the source is at EOF.
     *
     * @param bool $seekable
     *
     * @return bool
     */
    private function isEof($position)
    {
        return $position <= $this->sourceSize;
    }

    /**
     * Turns the provided source into a stream and stores it.
     *
     * If a string is provided, it is assumed to be a filename, otherwise, it
     * passes the value directly to `Psr7\Utils::streamFor()`.
     *
     * @param mixed $source
     *
     * @return Stream
     */
    protected function determineSourceSize($size)
    {
//        echo 'abstract downloader size: ' . $size;
        $this->sourceSize = $size;
//        $generator = function ($bytes) {
//            for ($i = 0; $i < $bytes; $i++) {
//                yield '.';
//            }
//        };
//
//        $iter = $generator($this->sourceSize);
//        $stream = Psr7\Utils::streamFor($iter);
//        $this->source = $stream;
    }

    protected function getNumberOfParts($partSize)
    {
        if ($this->sourceSize) {
            return ceil($this->sourceSize/$partSize);
        }
        return null;
    }
}