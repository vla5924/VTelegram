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
    
    public $parseMode = null;
    public $disableWebPagePreview = false;

    /**
     * @brief Constructor-initializer
     * @param string $text Text of the message
     * @todo Other parameters
     */
    public function __construct(string $text)
    {
        $this->text = $text;
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
