<?php

namespace VTg\BotModules;

use VTg\BotModules\PatternChecker;
use VTg\Objects\Message;

/**
 * @class DynamicCommands
 * @extends Bot
 * @brief Trait for dynamic commands mechanism
 * @details It can be useful e.g. if you want to use command to transfer identifiers or other data.
 * @warning This is a trait, not a class (unfortunately, Doxygen does not
 * support PHP traits so it looks like a class in documentation)
 */
trait DynamicCommands
{
    /**
     * @var array $commands
     * @brief Array with commands handlers
     * @details See VTgBot::$commands
     */
    protected static $commands = [];

    /**
     * @var array $dynamicCommands
     * @brief Array with dynamic commands handlers
     * @details See VTgBot::$commands
     */
    protected static $dynamicCommands = [];

    /**
     * @var callable|null $commandFallbackHandler
     * @brief Function for handling messages if they don't contain /commands
     * @details See VTgBot::$commandFallbackHandler
     */
    protected static $commandFallbackHandler = null;

    use PatternChecker;

    /**
     * @memberof DynamicCommands
     * @brief Registers a function as a handler for messages containing /dynamic_commands
     * @details A handler will be passed: VTgBotController object as first parameter, 
     * VTgMessage object as second parameter,  array with command itself and found 
     * %parameters as third parameter, string (part of message following the command)
     * as fourth parameter. So you can use it like this:
     * @code
     * VTgBot::registerDynamicCommandHandler('get_%d', function (VTgBotController $bot, VTgMessage $message, array $parameters, string $data) {
     *   $answer = 'Getting ' . $parameters[1] . ' for chat ' . $message->chat->id;
     *   $bot->execute(VTgAction::sendMessage($message->chat->id, $answer));
     * });
     * @endcode
     * As you can see, you can provide command parameters as typed placeholder (just like in printf function).
     * There are three placeholders available: 
     * @code{.txt} 
     * %d - integer (set of 0-9 symbols, e.g. 123), 
     * %s - letters string (set of a-z, A-Z symbols, e.g. hello), 
     * %a - letters and numbers string (set of 0-9, a-z, A-Z symbols, e.g. h1ello23)
     * @endcode
     * @param string $patternCommand Command you want to handle (don't mention '/', e.g. 'help', not '/help')
     * @param callable $handler Command handler [function (VTgBotController, VTgMessage, array, string)]
     */
    public static function registerDynamicCommandHandler(string $patternCommand, callable $handler): void
    {
        static::$commands['%DYNAMIC%'] = false;
        static::$dynamicCommands[$patternCommand] = $handler;
    }

    /**
     * @memberof DynamicCommands
     * @brief Hadles with a command if found in message
     * @details First things first, it will try to find if given command matches any dynamic command.
     * Then it will check other commands as usual.
     * @param Message $message Message data received from Telegram
     * @param string $command Command to handle
     * @param string $data A part of message following the command
     */
    protected static function handleCommand(Message $message, string $command, string $data = ""): void
    {
        foreach (static::$dynamicCommands as $patternCommand => $handler) {
            $parameters = [];
            if (self::checkMatch($patternCommand, $command, $parameters)) {
                ($handler)(static::makeController($message), $message, $parameters, $data);
                return;
            }
        }
        if (isset(static::$commands[$command])) {
            (static::$commands[$command])(static::makeController($message), $message, $data);
        } elseif (static::$commandFallbackHandler) {
            (static::$commandFallbackHandler)(static::makeController($message), $message, $command, $data);
        } else {
            static::handleMessageStandardly($message);
        }
    }
}
/**
 * @example DynamicCommandsBot.php
 * Example of how to create a bot with dynamic command handler using VTgDynamicCommands trait.
 */
