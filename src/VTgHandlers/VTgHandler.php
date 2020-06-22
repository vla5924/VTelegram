<?php

abstract class VTgHandler
{
    /**
     * @var callable|null $handler
     * @brief Function
     */
    protected $handler = null;

    const TYPE = 0;

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
     * @brief Calls before handling
     */
    static protected $preHandlers = [];

    static public function addPreHandler(string $name, callable $preHandler): void
    {
        self::$preHandlers[$name] = $preHandler;
    }

    static public function removePreHandler(string $name): void
    {
        if (isset(self::$preHandlers[$name]))
            unset(self::$preHandlers[$name]);
    }

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
