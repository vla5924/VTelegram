<?php

require_once __DIR__ . '/VTgHandler.php';
require_once __DIR__ . '/../VTgBotController.php';
require_once __DIR__ . '/../VTgObjects/VTgMessage.php';

class VTgStandardMessageHandler extends VTgHandler
{
    const TYPE = self::STANDARD_MESSAGE;

    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    }

    public function __invoke(VTgBotController $bot, VTgMessage $message)
    {
        ($this->handler)($bot, $message, self::preHandle(func_get_args()));
    }
}
