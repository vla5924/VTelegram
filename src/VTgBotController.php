<?php

require_once __DIR__ . '/VTgRequestor.php';
require_once __DIR__ . '/VTgMetaObjects/VTgAction.php';

/**
 * @brief Class to wrap some functions of VTgBot and related objects to pass to handlers
 */
class VTgBotController
{
    /**
     * @var VTgRequestor $tg
     * @brief Objects for direct requests to Telegram API
     */
    public $tg;

    public $preHandled = null;

    /**
     * @brief Constructor-initializer
     * @param VTgRequestor $tg Object for direct requests to Telegram API
     */
    public function __construct(VTgRequestor $tg, $preHandled = null)
    {
        $this->tg = $tg;
        $this->preHandled = $preHandled;
    }

    /**
     * @brief Execute an action
     * @param VTgAction $action Action to execute
     * @return mixed|null Return value from action
     */
    public function execute(VTgAction $action)
    {
        return $action->execute($this->tg);
    }
}
