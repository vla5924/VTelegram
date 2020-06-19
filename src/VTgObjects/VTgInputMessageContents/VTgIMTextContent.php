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
        return [
            'text' => $this->text,
            'parse_mode' => $this->parseMode,
            'disable_web_page_preview' => $this->disableWebPagePreview
        ];
    }
}
