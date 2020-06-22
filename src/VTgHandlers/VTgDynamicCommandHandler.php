<?php

require_once __DIR__ . '/VTgHandler.php';
require_once __DIR__ . '/../VTgBotController.php';
require_once __DIR__ . '/../VTgObjects/VTgMessage.php';

class VTgDynamicCommandHandler extends VTgHandler
{
    const TYPE = self::DYNAMIC_COMMAND;

    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    }

    public function __invoke(VTgBotController $bot, VTgMessage $message, array $parameters, string $data)
    {
        ($this->handler)($bot, $message, $parameters, $data, self::preHandle(func_get_args()));
    }
}
