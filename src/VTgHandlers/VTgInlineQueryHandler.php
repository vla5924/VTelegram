<?php

require_once __DIR__ . '/VTgHandler.php';
require_once __DIR__ . '/../VTgBotController.php';
require_once __DIR__ . '/../VTgObjects/VTgInlineQuery.php';

class VTgInlineQueryHandler extends VTgHandler
{
    const TYPE = self::INLINE_QUERY;

    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    }

    public function __invoke(VTgBotController $bot, VTgInlineQuery $inlineQuery)
    {
        ($this->handler)($bot, $inlineQuery, self::preHandle($bot, $inlineQuery));
    }
}
