<?php

require_once __DIR__ . '/../VTgBot.php';

/**
 * @class VTgDynamicCommands
 * @extends VTgBot
 * @brief Trait for dynamic commands mechanism
 * @details It can be useful e.g. if you want to use command to transfer identifiers or other data.
 * @warning This is a trait, not a class (unfortunately, Doxygen does not
 * support PHP traits so it looks like a class in documentation)
 */
trait VTgDynamicCommands
{
    /**
     * @var array $commands
     * @brief Array with commands handlers
     * @details See VTgBot::$commands
     */
    static protected $commands = [];

    /**
     * @var array $dynamicCommands
     * @brief Array with dynamic commands handlers
     * @details See VTgBot::$commands
     */
    static protected $dynamicCommands = [];

    /**
     * @memberof VTgDynamicCommands
     * @brief Checks if a command matches a given pattern
     * @param string $patternCommand Pattern command (will be converted into regular expresson)
     * @param string $controlCommand Command to check
     * @param [out] array $matchParameters Array with command itself and found %parameters
     * @return bool True if a command matches a pattern
     */
    static protected function checkMatch(string $patternCommand, string $controlCommand, array &$matchParameters = null): bool
    {
        $regExp = '/^' . str_replace(['%d', '%s', '%a'], ['([0-9]+)', '([A-Za-z]+)', '([0-9a-zA-Z]+)'], $patternCommand) . '$/';
        return preg_match($regExp, $controlCommand, $matchParameters) === 1;
    }

    /**
     * @memberof VTgDynamicCommands
     * @brief Registers a function as a handler for messages containing /dynamic_commands
     * @details A handler will be passed: VTgMessage object as first parameter, 
     * array with command itself and found %parameters as second parameter,
     * string (part of message following the command) as third parameter. Then, 
     * it must return VTgAction object. So you can use it like this:
     * @code
     * VTgBot::registerDynamicCommandHandler('get_%d', function (VTgMessage &$message, array $parameters, string $data) {
     *   $answer = 'Getting ' . $parameters[1] . ' for chat ' . $message->chat->id;
     *   return VTgAction::sendMessage($message->chat->id, $answer);
     * });
     * @endcode
     * As you can see, you can provide command parameters as typed placeholder (just like in printf function).
     * There are three placeholders available: 
     * %d - integer (set of 0-9 symbols, e.g. 123), 
     * %s - letters string (set of a-z, A-Z symbols, e.g. hello), 
     * %a - letters and numbers string (set of 0-9, a-z, A-Z symbols, e.g. h1ello23)
     * @param string $command Command you want to handle (don't mention '/', e.g. 'help', not '/help')
     * @param callable $handler Command handler (function(VTgMessage, array, string):VTgAction)
     */
    static public function registerDynamicCommandHandler(string $patternCommand, callable $handler): void
    {
        static::$commands['%DYNAMIC%'] = false;
        static::$dynamicCommands[$patternCommand] = $handler;
    }
    
    /**
     * @memberof VTgDynamicCommands
     * @brief Hadles with a command if found in message
     * @details First things first, it will try to find if given command matches any dynamic command.
     * Then it will check other commands as usual.
     * @param VTgMessage $message Message data received from Telegram
     * @param string $command Command to handle
     * @param string $data A part of message following the command
     * @return VTgAction Action for how to handle with command
     */
    static protected function handleCommand(VTgMessage $message, string $command, string $data = ""): VTgAction
    {
        foreach (static::$dynamicCommands as $patternCommand => $handler) {
            $parameters = [];
            if (self::checkMatch($patternCommand, $command, $parameters))
                return ($handler)($message, $parameters, $data);
        }
        if (!isset(static::$commands[$command]))
            return VTgAction::doNothing();
        return (static::$commands[$command])($message, $data);
    }
}
