<?php

namespace VTg\Objects;

use VTg\Objects\BaseObject;
use VTg\Requestor;

/**
 * @brief Class to represent file stored on Telegram servers
 */
class File extends BaseObject
{
    /**
     * @var string $id
     * @brief Identifier for this file, which can be used to download or reuse the file
     */
    public $id;

    /**
     * @var string $uniqueId
     * @brief Unique identifier for this file
     * @details This identifier is supposed to be the same over time and for different bots. Can't be used to download or reuse the file.
     */
    public $uniqueId;

    /**
     * @var string|null $fileSize
     * @brief File size, if known
     */
    public $fileSize = null;

    /**
     * @var string|null $filePath
     * @brief File path
     */
    public $filePath = null;

    /**
     * @brief Constructor-initializer
     * @param array $data JSON-decoded callback query data received from Telegram
     */
    public function __construct(array $data)
    {
        $this->id = $data["file_id"];
        $this->uniqueId = $data["unique_id"];
        $this->fileSize = $data["file_size"] ?? null;
        $this->filePath = $data["file_path"] ?? null;
    }

    /**
     * @brief Checks if file can be downloaded
     * @return bool True if file can be downloaded (has file_path)
     */
    public function canBeDownloaded(): bool
    {
        if ($this->filePath)
            return true;
        return false;
    }

    /**
     * @brief The file can be downloaded via the link provided by this method
     * @param string $token Bot API token
     * @return string URL for downloading
     */
    public function getDownloadUrl(string $token): string
    {
        return 'https://api.telegram.org/file/bot ' . $token . '/' . $this->filePath;
    }

    /**
     * @brief Downloads and returns file contents
     * @param string $token Bot API token
     * @return mixed File contents
     */
    public function getContents(string $token)
    {
        return file_get_contents($this->getDownloadUrl($token));
    }

    /**
     * @brief Downloads and saves file contents
     * @param string $token Bot API token
     * @param string $destinationPath Path to result file to save
     * @return mixed The number of bytes that were written to the file, or false on failure
     */
    public function putContents(string $token, string $destinationPath)
    {
        return file_put_contents($destinationPath, $this->getDownloadUrl($token));
    }

    /**
     * @brief Gets file path and unique ID from Telegram for current file object by ID
     * @param VTgRequestor $tg Requestor for call getFile method
     * @return VTgFile Itself
     */
    public function getById(Requestor $tg): self
    {
        $result = $tg->getFile($this->id);
        if ($result->ok) {
            $this->uniqueId = $result->object->uniqueId;
            $this->filePath = $result->object->filePath;
        }
        return $this;
    }
}
