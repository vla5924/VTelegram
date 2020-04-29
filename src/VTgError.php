<?php

/**
 * @brief Class to store information about error while making request to Telegram Bot API
 */
class VTgError
{
    /**
     * @var int $code
     * @brief Error code
     */
    public $code;

    /**
     * @var string $description
     * @brief Error description
     */
    public $description;

    /**
     * @brief Constructor-initializer
     * @param int $code Error code
     * @param string $description Error description
     */
    public function __construct(int $code, string $description = "")
    {
        $this->code = $code;
        $this->description = $description;
    }

    /**
     * @brief Typecast for string
     * @return string Information in format "Error <code>: <description>"
     */
    public function __toString(): string
    {
        return "Error " . $this->code . ": " . $this->description;
    }
}