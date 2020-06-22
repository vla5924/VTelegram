<?php

require_once __DIR__ . '/VTgHandler.php';
require_once __DIR__ . '/../VTgBotController.php';
require_once __DIR__ . '/../VTgObjects/VTgCallbackQuery.php';

class VTgCallbackQueryHandler extends VTgHandler
{
    const TYPE = self::CALLBACK_QUERY;

    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    }

    public function __invoke(VTgBotController $bot, VTgCallbackQuery $callbackQuery)
    {
        ($this->handler)($bot, $callbackQuery, self::preHandle($bot, $callbackQuery));
    }
}
