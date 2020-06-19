<?php

require_once __DIR__ . '/VTgIMContent.php';

class VTgIMTextContent extends VTgIMContent
{
    public $text;
    public $parseMode = null;
    public $disableWebPagePreview = false;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function toArray(): array
    {
        $array = [
            'message_text' => $this->text,
            'disable_web_page_preview' => $this->disableWebPagePreview
        ];
        if($this->parseMode)
            $array['parse_mode'] = $this->parseMode;
        return $array;
    }
}
