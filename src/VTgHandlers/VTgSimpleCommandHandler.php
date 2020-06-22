<?php

require_once __DIR__ . '/VTgHandler.php';
require_once __DIR__ . '/../VTgBotController.php';
require_once __DIR__ . '/../VTgObjects/VTgMessage.php';

class VTgSimpleCommandHandler extends VTgHandler
{
    const TYPE = self::SIMPLE_COMMAND;
    
    const MARKDOWN = ['parse_mode' => 'Markdown'];
    const NO_PREVIEW = ['disable_web_page_preview' => true];
    const MARKDOWN_AND_NO_PREVIEW = ['parse_mode' => 'Markdown', 'disable_web_page_preview' => true];

    public $text;
    public $extraParameters = [];

    public function __construct(string $text, array $extraParameters = [])
    {
        $this->text = $text;
        $this->extraParameters = $extraParameters;
    }

    public function __invoke(VTgBotController $bot, VTgMessage $message, string $data)
    {
        self::preHandle($bot, $message, $data);
        $bot->execute($message->reply($this->text, false, $this->extraParameters));
    }
}
