<?php

namespace VTg;

use VTg\Requestor;
use VTg\MetaObjects\Action;

/**
 * @brief Class to wrap some functions of VTgBot and related objects to pass to handlers
 */
class BotController
{
    /**
     * @var Requestor $tg
     * @brief Objects for direct requests to Telegram API
     */
    public $tg;

    /**
     * @var mixed|null $preHandled
     * @brief Return value of pre-handler function (if presented)
     */
    public $preHandled = null;

    /**
     * @brief Constructor-initializer
     * @param Requestor $tg Object for direct requests to Telegram API
     * @param mixed|null $preHandled Return value of pre-handler function (if presented)
     */
    public function __construct(Requestor $tg, $preHandled = null)
    {
        $this->tg = $tg;
        $this->preHandled = $preHandled;
    }

    /**
     * @brief Execute an action
     * @param Action $action Action to execute
     * @return mixed|null Return value from action
     */
    public function execute(Action $action)
    {
        return $action->execute($this->tg);
    }
}
