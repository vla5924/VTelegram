<?php

require_once __DIR__ . '/../VTgInputMessageContents/VTgIMContent.php';

class VTgIQArticle
{
    const TYPE = 'article';
    public $id;
    public $title;
    public $inputMessageContent;
    public $extraParameters = [];

    public function __construct(string $id, string $title, VTgIMContent $inputMessageContent, array $extraParameters = [])
    {
        $this->id = $id;
        $this->title = $title;
        $this->inputMessageContent = $inputMessageContent;
        $this->extraParameters = $extraParameters;
    }

    public function toArray(): array
    {
        return array_merge([
            'type' => self::TYPE,
            'id' => $this->id,
            'title' => $this->title,
            'input_message_content' => $this->inputMessageContent->toArray(),
        ], $this->extraParameters);
    }
}