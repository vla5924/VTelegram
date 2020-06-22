<?php

require_once __DIR__ . '/VTgHandler.php';
require_once __DIR__ . '/../VTgBotController.php';
require_once __DIR__ . '/../VTgObjects/VTgMessage.php';

class VTgSimpleCommandHandler extends VTgHandler
{
    const TYPE = self::SIMPLE_COMMAND;

    public $text;
    public $extraParameters = [];

    public function __construct(string $text, array $extraParameters = [])
    {
        $this->text = $text;
    }

    public function __invoke(VTgBotController $bot, VTgMessage $message, string $data)
    {
        self::preHandle($bot, $message, $data);
        $bot->execute($message->reply($this->text, false, $this->extraParameters));
    }
}
