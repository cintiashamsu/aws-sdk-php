<?php
namespace Aws\Multipart;

use Aws\AwsClientInterface as Client;
use Aws\CommandInterface;
use Aws\CommandPool;
use Aws\Exception\AwsException;
use Aws\Exception\MultipartDownloadException;
use Aws\Result;
use Aws\ResultInterface;
use GuzzleHttp\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use InvalidArgumentException as IAE;
use Psr\Http\Message\RequestInterface;

/**
 * Encapsulates the execution of a multipart download to S3.
 *
 * @internal
 */
abstract class AbstractDownloadManager implements Promise\PromisorInterface
{
    const DEFAULT_CONCURRENCY = 5;

    /** @var array Default values for base multipart configuration */
    private static $defaultConfig = [
        'part_size'           => null,
        'state'               => null,
        'concurrency'         => self::DEFAULT_CONCURRENCY,
        'prepare_data_source' => null,
        'before_initiate'     => null,
        'before_download'     => null,
        'before_complete'     => null,
        'exception_class'     => 'Aws\Exception\MultipartDownloadException',
    ];

    /** @var Client Client used for the download. */
    protected $client;

    /** @var array Configuration used to perform the download. */
    protected $config;

    /** @var array Service-specific information about the download workflow. */
    protected $info;

    /** @var PromiseInterface Promise that represents the multipart download. */
    protected $promise;

    /** @var DownloadState State used to manage the download. */
    protected $state;

    /**
     * @param Client $client
     * @param array  $config
     */
    public function __construct(Client $client, array $config = [])
    {
        $this->client = $client;
        $this->info = $this->loadDownloadWorkflowInfo();
        $this->config = $config + self::$defaultConfig;
        $this->state = $this->determineState();
    }

    /**
     * Returns the current state of the download.
     *
     * @return DownloadState
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Download the source using multipart download operations.
     *
     * @return Result The result of the GetObject operations.
     * @throws \LogicException if the download is already complete or aborted.
     * @throws MultipartDownloadException if a download operation fails.
     */
    public function download()
    {
        return $this->promise()->wait();
    }

    /**
     * Download the source asynchronously using multipart download operations.
     *
     * @return PromiseInterface
     */
    public function promise()
    {
        if ($this->promise) {
            return $this->promise;
        }

        return $this->promise = Promise\Coroutine::of(function () {
            // Initiate the download.
            if ($this->state->isCompleted()) {
                throw new \LogicException('This multipart download has already '
                    . 'been completed or aborted.'
                );
            }

            if (!$this->state->isInitiated()) {
                // Execute the prepare callback.
                if (is_callable($this->config["prepare_data_source"])) {
                    $this->config["prepare_data_source"]();
                }
                $type = $this->getDownloadType();
                $result = (yield $this->execCommand('initiate', $this->getInitiateParams($type)));
                $this->determineSourceSize($result['ContentRange']);
                if ($this->getState()->displayProgress) {
                    $this->state->setProgressThresholds($this->sourceSize);
                }
                $this->setStreamPositionArray();
                $this->state->setStatus(DownloadState::INITIATED);
                if (isset($type['multipart'])){
                    $this->handleResult(1, $result);
                } else {
                    $this->handleResult($type['configParam'], $result);
                }
            }
            // end download if PartNumber or Range is set, or object is only one part total.
            if (isset($this->config['partnumber'])
                or isset($this->config['range'])
                or $result['PartsCount']==1){
                $this->state->setStatus(DownloadState::COMPLETED);
                if ($this->getState()->displayProgress) {
                    echo end($this->state->progressBar);
                }
            } else {
                // Create a command pool from a generator that yields DownloadPart
                // commands for each download part.
                $resultHandler = $this->getResultHandler($errors);
                $commands = new CommandPool(
                    $this->client,
                    $this->getDownloadCommands($resultHandler),
                    [
                        'concurrency' => $this->config['concurrency'],
                        'before' => $this->config['before_download'],
                    ]
                );

                // Execute the pool of commands concurrently, and process errors.
                yield $commands->promise();
                if ($errors) {
                    throw new $this->config['exception_class']($this->state, $errors);
                }

                // Complete the multipart download.
                $this->state->setStatus(DownloadState::COMPLETED);
            }})->otherwise($this->buildFailureCatch());
    }

    private function transformException($e)
    {
        // Throw errors from the operations as a specific Multipart error.
        if ($e instanceof AwsException) {
            $e = new $this->config['exception_class']($this->state, $e);
        }
        throw $e;
    }

    private function buildFailureCatch()
    {
        if (interface_exists("Throwable")) {
            return function (\Throwable $e) {
                return $this->transformException($e);
            };
        } else {
            return function (\Exception $e) {
                return $this->transformException($e);
            };
        }
    }

    protected function getConfig()
    {
        return $this->config;
    }

    /**
     * Provides service-specific information about the multipart download
     * workflow.
     *
     * This array of data should include the keys: 'command', 'id', and 'part_num'.
     *
     * @return array
     */
    abstract protected function loadDownloadWorkflowInfo();

    abstract protected function determineSourceSize($size);

    /**
     * Determines the part size to use for download parts.
     *
     * Examines the provided partSize value and the source to determine the
     * best possible part size.
     *
     * @throws \InvalidArgumentException if the part size is invalid.
     *
     * @return int
     */
    abstract protected function determinePartSize();

    /**
     * Uses information from the Command and Result to determine which part was
     * downloaded and mark it as downloaded in the download's state, and sends
     * information to be written to the destination stream.
     *
     * @param CommandInterface $command
     * @param ResultInterface  $result
     */
    abstract protected function handleResult(
        CommandInterface $command,
        ResultInterface $result
    );

    /**
     * Gets the service-specific parameters used to initiate the download.
     *
     * @param array  $configType Service-specific params for the operation.
     *
     * @return array
     */
    abstract protected function getInitiateParams($configType);

    /**
     * Gets the service-specific parameters used to complete the download
     * from the config.
     *
     * @return array
     */
    abstract protected function getDownloadType();

    /**
     * Based on the config and service-specific workflow info, creates a
     * `Promise` for a `DownloadState` object.
     *
     * @return PromiseInterface A `Promise` that resolves to a `DownloadState`.
     */
    private function determineState()
    {
        // If the state was provided via config, then just use it.
        if ($this->config['state'] instanceof DownloadState) {
            return $this->config['state'];
        }

        // Otherwise, construct a new state from the provided identifiers.
        // TODO delete id logic
        $required = $this->info['id'];
        $id = [];
        foreach ($required as $key => $param) {
            if (!$this->config[$key]) {
                throw new IAE('You must provide a value for "' . $key . '" in '
                    . 'your config for the MultipartDownloader for '
                    . $this->client->getApi()->getServiceFullName() . '.');
            }
            $id[$param] = $this->config[$key];
        }
        $state = new DownloadState($id);
        $state->setPartSize($this->determinePartSize());

        return $state;
    }

    /**
     * Executes a MUP command with all the parameters for the operation.
     *
     * @param string $operation Name of the operation.
     * @param array  $params    Service-specific params for the operation.
     *
     * @return PromiseInterface
     */
    protected function execCommand($operation, array $params)
    {
        // Create the command.
        $command = $this->client->getCommand(
            $this->info['command'][$operation],
            $params + $this->state->getId()
        );

        // Execute the before callback.
        if (is_callable($this->config["before_{$operation}"])) {
            $this->config["before_{$operation}"]($command);
        }

        // Execute the command asynchronously and return the promise.
        return $this->client->executeAsync($command);
    }

    /**
     * Returns a middleware for processing responses of part download operations.
     *
     * - Adds an onFulfilled callback that calls the service-specific
     *   handleResult method on the Result of the operation.
     * - Adds an onRejected callback that adds the error to an array of errors.
     * - Has a passedByRef $errors arg that the exceptions get added to. The
     *   caller should use that &$errors array to do error handling.
     *
     * @param array $errors Errors from download operations are added to this.
     *
     * @return callable
     */
    protected function getResultHandler(&$errors = [])
    {
        return function (callable $handler) use (&$errors) {
            return function (
                CommandInterface $command,
                RequestInterface $request = null
            ) use ($handler, &$errors) {
                return $handler($command, $request)->then(
                    function (ResultInterface $result) use ($command) {
                        $this->handleResult($command, $result);
                        return $result;
                    },
                    function (AwsException $e) use (&$errors) {
                        $errors[$e->getCommand()[$this->info['part_num']]] = $e;
                        return new Result();
                    }
                );
            };
        };
    }

    /**
     * Creates a generator that yields part data for the download's source.
     *
     * Yields associative arrays of parameters that are ultimately merged in
     * with others to form the complete parameters of a  command. This can
     * include the PartNumber or Range parameter.
     *
     * @param callable $resultHandler
     *
     * @return \Generator
     */
    abstract protected function getDownloadCommands(callable $resultHandler);
}
