<?php

/**
 * @brief Base class for handlers
 */
abstract class VTgHandler
{
    /**
     * @var callable|null $handler
     * @brief Handler function
     */
    protected $handler = null;

    const TYPE = 0; ///< Code of handler type

    const UNDEFINED = 0;
    const CALLBACK_QUERY = 1;
    const CHOSEN_INLINE_RESULT = 2;
    const COMMAND_FALLBACK = 3;
    const COMMAND = 4;
    const DYNAMIC_COMMAND = 5;
    const DYNAMIC_CALLBACK_QUERY = 6;
    const INLINE_QUERY = 7;
    const SIMPLE_COMMAND = 8;
    const STANDARD_MESSAGE = 9;

    /**
     * @var array $preHandlers
     * @brief Array of callables, will be called before main handler
     */
    static protected $preHandlers = [];

    /**
     * @brief Adds pre-handler
     * @param string $name Name of pre-handler
     * @param callable $preHandler Pre-handler function
     */
    static public function addPreHandler(string $name, callable $preHandler): void
    {
        self::$preHandlers[$name] = $preHandler;
    }

    /**
     * @brief Removes pre-handler
     * @param string $name Name of pre-handler
     */
    static public function removePreHandler(string $name): void
    {
        if (isset(self::$preHandlers[$name]))
            unset(self::$preHandlers[$name]);
    }

    /**
     * @brief Calls pre-handlers
     * @details Each handler will be passed integer code of type and all parameters that
     * will be then passed to main handlers (e.g. for callback query handler it will be 
     * VTgBotController and VTgCallbackQuery)
     * @param mixed ...$args Arguments for pre-handler functions
     * @return array Array of return values of pre-handlers (keys are pre-handlers' names)
     */
    protected function preHandle(...$args): array
    {
        $result = [];
        foreach (self::$preHandlers as $name => $handler)
            if ($handler)
                if($ret = $handler(static::TYPE, ...$args) ?? false)
                    $result[$name] = $ret;
        return $result;
    }
}
