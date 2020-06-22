<?php

require_once __DIR__ . '/VTgHandler.php';
require_once __DIR__ . '/../VTgBotController.php';
require_once __DIR__ . '/../VTgObjects/VTgMessage.php';

class VTgCommandFallbackHandler extends VTgHandler
{
    const TYPE = self::COMMAND_FALLBACK;

    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    }

    public function __invoke(VTgBotController $bot, VTgMessage $message, string $command, string $data)
    {
        ($this->handler)($bot, $message, $command, $data, self::preHandle(func_get_args()));
    }
}
