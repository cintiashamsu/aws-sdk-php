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

    public $progressBar = [
        "Transfer initiated...\n|                    | 0.0%\n",
        "|==                  | 12.5%\n",
        "|=====               | 25.0%\n",
        "|=======             | 37.5%\n",
        "|==========          | 50.0%\n",
        "|============        | 62.5%\n",
        "|===============     | 75.0%\n",
        "|=================   | 87.5%\n",
        "|====================| 100.0%\nTransfer complete!\n"
    ];

    /** @var array Params used to identity the download. */
    private $id;

    /** @var int Part size being used by the download. */
    public $partSize;

    /** @var array Parts that have been downloaded. */
    private $downloadedParts = [];

    /** @var int Identifies the status the download. */
    private $status = self::CREATED;

    /** @var array Thresholds for progress of the upload. */
    private $progressThresholds = [];

    /** @var boolean Determines status for tracking the upload */
    public $displayProgress = false;

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
//    public function setDownloadId($key, $value)
//    {
//        $this->id[$key] = $value;
//    }

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
     * Sets the 1/8th thresholds array. $totalSize is only sent if
     * 'track_download' is true.
     *
     * @param $totalSize numeric Size of object to download.
     *
     * @return array
     */
    public function setProgressThresholds($totalSize)
    {
        if(!is_numeric($totalSize)) {
            throw new \InvalidArgumentException(
                'The total size of the upload must be a number.'
            );
        }

        $this->progressThresholds[0] = 0;
        for ($i=1;$i<=8;$i++) {
            $this->progressThresholds []= round($totalSize*($i/8));
        }
        return $this->progressThresholds;
    }

    /**
     * Prints progress of download.
     *
     * @param $totalUploaded numeric Size of download so far.
     */
    public function getDisplayProgress($totalUploaded)
    {
        if (!is_numeric($totalUploaded)) {
            throw new \InvalidArgumentException(
                'The size of the bytes being uploaded must be a number.'
            );
        }

        if ($this->displayProgress) {
            while (!empty($this->progressBar)
                && $totalUploaded >= $this->progressThresholds[0]) {
                echo array_shift($this->progressBar);
                array_shift($this->progressThresholds);
            }
        }
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