<?php

require_once __DIR__ . '/VTgIMC.php';

/**
 * @brief Class for input message text content in inline query results
 */
class VTgIMCText extends VTgIMC
{
    /**
     * @var string $text
     * @brief Text of the message
     */
    public $text;
    
    /**
     * @var string|null $parseMode
     * @brief Parse mode
     */
    public $parseMode = null;

    /**
     * @var bool $disableWebPagePreview
     * @brief Disable web page preview for links in message text
     */
    public $disableWebPagePreview = false;

    /**
     * @brief Constructor-initializer
     * @param string $text Text of the message
     * @param string $parseMode Parse mode
     * @param bool $disableWebPagePreview Disable web page preview for links in message text
     */
    public function __construct(string $text, string $parseMode = null, bool $disableWebPagePreview = false)
    {
        $this->text = $text;
        $this->parseMode = $parseMode ?? null;
        $this->disableWebPagePreview = $disableWebPagePreview;
    }

    /**
     * @brief Converts object data to array
     * @return array Array
     */
    public function toArray(): array
    {
        $array = [
            'message_text' => $this->text,
            'disable_web_page_preview' => $this->disableWebPagePreview
        ];
        if ($this->parseMode)
            $array['parse_mode'] = $this->parseMode;
        return $array;
    }
}
