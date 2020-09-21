<?php

namespace VTg\BotModules;

use VTg\BotController;
use VTg\Objects\Message;

/**
 * @class SimpleCommands
 * @extends VTgBot
 * @brief Trait for simple commands mechanism
 * @details It can be useful if you want just to send text messages to some commands (without other actions)
 * @warning This is a trait, not a class (unfortunately, Doxygen does not
 * support PHP traits so it looks like a class in documentation)
 */
trait SimpleCommands
{
    /**
     * @var array $commands
     * @brief Array with commands handlers
     * @details See Bot::$commands
     */
    protected static $commands = [];

    /**
     * @memberof SimpleCommands
     * @brief Registers a simple handler for messages containing /commands
     * @param string $command Command you want to handle (don't mention '/', e.g. 'help', not '/help')
     * @param string $text Message text to answer to the command
     * @param array $extraParameters Other parameters for sending message if needed
     */
    public static function registerSimpleCommandHandler(string $command, string $text, array $extraParameters = []): void
    {
        static::$commands[$command] = function (BotController $bot, Message $message) use ($text, $extraParameters) {
            $bot->execute($message->answer($text, $extraParameters));
        };
    }
}
