<?php

require_once __DIR__ . '/VTgHandler.php';
require_once __DIR__ . '/../VTgBotController.php';
require_once __DIR__ . '/../VTgObjects/VTgMessage.php';

class VTgCommandHandler extends VTgHandler
{
    const TYPE = self::COMMAND;

    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    }

    public function __invoke(VTgBotController $bot, VTgMessage $message, string $data)
    {
        ($this->handler)($bot, $message, $data, self::preHandle($bot, $message, $data));
    }
}
