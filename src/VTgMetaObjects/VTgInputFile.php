<?php

/**
 * @brief Class used to reprsesent InputFile
 */
class VTgInputFile
{
    /**
     * @var int $type
     * @brief Type of data describing the file
     */
    public $type = 0;

    /**
     * @var string|null $data
     * @brief File descriptor (ID, URL or local path to file)
     */
    public $data = null;

    const TYPE__UNKNOWN    = 0; ///< Unknown type
    const TYPE__FILE_ID    = 1; ///< Use if the file is already stored somewhere on the Telegram servers
    const TYPE__URL        = 2; ///< Use an HTTP URL for Telegram to get a file from the Internet
    const TYPE__LOCAL_FILE = 3; ///< Use to upload a new file (with multipart/form-data)

    /**
     * @brief Constructor-initializer
     * @param int $type Type of data describing the file
     * @param string $data File descriptor
     */
    public function __construct(int $type, string $data)
    {
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * @brief Checks if file can be described for Telegram as string
     * @details True if data property contains file_id or HTTP URL
     * @return bool True if file can be described for Telegram as string
     */
    public function isString(): bool
    {
        return ($this->type === self::TYPE__FILE_ID) || ($this->type === self::TYPE__URL);
    }

    /**
     * @brief Checks if file can be described for Telegram as CURLFile
     * @details True if data property contains local path and therefore must be passed as multipart/form-data
     * @return bool True if file can be described for Telegram as CURLFile
     */
    public function isCurlFile(): bool
    {
        return ($this->type === self::TYPE__LOCAL_FILE);
    }

    /**
     * @brief Gets data in correct format if possible
     * @return mixed String, CURLFile of false can be returned
     */
    public function get()
    {
        if ($this->isString())
            return $this->data;
        if ($this->isCurlFile())
            return new CURLFile($this->data);
        return false;
    }

    /**
     * @brief Creates object which contains file_id
     * @param string $fileId Telegram file identifier
     * @return VTgInputFile Input file meta object
     */
    static public function fileId(string $fileId): self
    {
        return new self(self::TYPE__FILE_ID, $fileId);
    }

    /**
     * @brief Creates object which contains web url
     * @param string $url HTTP URL
     * @return VTgInputFile Input file meta object
     */
    static public function url(string $url): self
    {
        return new self(self::TYPE__URL, $url);
    }

    /**
     * @brief Creates object which contains local path
     * @param string $fileId Local path to file
     * @return VTgInputFile Input file meta object
     */
    static public function localFile(string $localFile): self
    {
        return new self(self::TYPE__LOCAL_FILE, $localFile);
    }
}
