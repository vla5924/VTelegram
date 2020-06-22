<?php

require_once __DIR__ . '/VTgHandler.php';
require_once __DIR__ . '/../VTgBotController.php';
require_once __DIR__ . '/../VTgObjects/VTgCallbackQuery.php';

class VTgDynamicCQHandler extends VTgHandler
{
    const TYPE = self::DYNAMIC_CALLBACK_QUERY;

    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    }

    public function __invoke(VTgBotController $bot, VTgCallbackQuery $callbackQuery, array $parameters)
    {
        ($this->handler)($bot, $callbackQuery, $parameters, self::preHandle($bot, $callbackQuery, $parameters));
    }
}
