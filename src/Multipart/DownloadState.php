<?php
namespace Aws\Multipart;

/**
 * Representation of the multipart download.
 *
 * This object keeps track of the state of the download, including the status and
 * which parts have been downloaded.
 */
class DownloadState
{
    const CREATED = 0;
    const INITIATED = 1;
    const COMPLETED = 2;

    /** @var array Params used to identity the download. */
    private $id;

    /** @var int Part size being used by the download. */
    private $partSize;

    /** @var array Parts that have been downloaded. */
    private $downloadedParts = [];

    /** @var int Identifies the status the download. */
    private $status = self::CREATED;

    /**
     * @param array $id Params used to identity the download.
     */
    public function __construct(array $id)
    {
        $this->id = $id;
    }

    /**
     * Get the download's ID, which is a tuple of parameters that can uniquely
     * identify the download.
     *
     * @return array
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set's the "download_id", or 3rd part of the download's ID. This typically
     * only needs to be done after initiating a download.
     *
     * @param string $key   The param key of the download_id.
     * @param string $value The param value of the download_id.
     */
    public function setDownloadId($key, $value)
    {
        $this->id[$key] = $value;
    }

    /**
     * Get the part size.
     *
     * @return int
     */
    public function getPartSize()
    {
        return $this->partSize;
    }

    /**
     * Set the part size.
     *
     * @param $partSize int Size of download parts.
     */
    public function setPartSize($partSize)
    {
        $this->partSize = $partSize;
    }

    /**
     * Marks a part as being downloaded.
     *
     * @param string   $partNumber The part number.
     * @param array    $partData   Data from the download operation that needs to be
     *                          recalled during the complete operation.
     */
    public function markPartAsDownloaded($partNumber, array $partData = [])
    {
        $this->downloadedParts[$partNumber] = $partData;
    }

    /**
     * Returns whether a part has been downloaded.
     *
     * @param int $partNumber The part number.
     *
     * @return bool
     */
    public function hasPartBeenDownloaded($partNumber)
    {
        return isset($this->downloadedParts[$partNumber]);
    }

    /**
     * Returns a sorted list of all the downloaded parts.
     *
     * @return array
     */
    public function getDownloadedParts()
    {
        ksort($this->downloadedParts);
        return $this->downloadedParts;
    }

    /**
     * Set the status of the download.
     *
     * @param int $status Status is an integer code defined by the constants
     *                    CREATED, INITIATED, and COMPLETED on this class.
     */

    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Determines whether the download state is in the INITIATED status.
     *
     * @return bool
     */
    public function isInitiated()
    {
        return $this->status === self::INITIATED;
    }

    /**
     * Determines whether the download state is in the COMPLETED status.
     *
     * @return bool
     */
    public function isCompleted()
    {
        return $this->status === self::COMPLETED;
    }
}