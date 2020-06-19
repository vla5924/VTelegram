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
     * @brief File descriptor
     */
    public $data = null;

    const TYPE__UNKNOWN    = 0; ///< Unknown type
    const TYPE__FILE_ID    = 1; ///< Use if the file is already stored somewhere on the Telegram servers
    const TYPE__URL        = 2; ///< Use an HTTP URL for Telegram to get a file from the Internet
    const TYPE__LOCAL_FILE = 3; ///< Use to upload a new file (with multipart/form-data)

    public function __construct(int $type, string $data)
    {
        $this->type = $type;
        $this->data = $data;
    }

    public function useAsString(): bool
    {
        return ($this->type === self::TYPE__FILE_ID) || ($this->type === self::TYPE__URL);
    }

    public function useAsCURLFile(): bool
    {
        return ($this->type === self::TYPE__LOCAL_FILE);
    }

    /**
     * @brief Gets data in correct format if possible
     * @return mixed String, CURLFile of FALSE can be returned
     */
    public function get()
    {
        if ($this->useAsString())
            return $this->data;
        if ($this->useAsCURLFile())
            return new CURLFile($this->data);
        return false;
    }

    static public function fileId(string $data): self
    {
        return new self(self::TYPE__FILE_ID, $data);
    }

    static public function url(string $data): self
    {
        return new self(self::TYPE__URL, $data);
    }

    static public function localFile(string $data): self
    {
        return new self(self::TYPE__LOCAL_FILE, $data);
    }
}
