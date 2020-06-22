<?php

require_once __DIR__ . '/VTgHandler.php';
require_once __DIR__ . '/../VTgBotController.php';
require_once __DIR__ . '/../VTgObjects/VTgChosenInlineResult.php';

class VTgChosenInlineResultHandler extends VTgHandler
{
    const TYPE = self::CHOSEN_INLINE_RESULT;

    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    }

    public function __invoke(VTgBotController $bot, VTgChosenInlineResult $inlineResult)
    {
        ($this->handler)($bot, $inlineResult, self::preHandle($bot, $inlineResult));
    }
}
