<?php

require_once __DIR__ . '/../VTgBot.php';

/**
 * @class VTgSimpleCommands
 * @extends VTgBot
 * @brief Trait for simple commands mechanism
 * @details It can be useful if you want just to send text messages to some commands (without other actions)
 * @warning This is a trait, not a class (unfortunately, Doxygen does not
 * support PHP traits so it looks like a class in documentation)
 */
trait VTgSimpleCommands
{
    /**
     * @var array $commands
     * @brief Array with commands handlers
     * @details See VTgBot::$commands
     */
    protected static $commands = [];

    /**
     * @memberof VTgSimpleCommands
     * @brief Registers a simple handler for messages containing /commands
     * @param string $command Command you want to handle (don't mention '/', e.g. 'help', not '/help')
     * @param string $text Message text to answer to the command
     */
    public static function registerSimpleCommandHandler(string $command, string $text, array $extraParameters = []): void
    {
        static::$commands[$command] = function (VTgBotController $bot, VTgMessage $message) use ($text, $extraParameters) {
            $bot->execute($message->answer($text, $extraParameters));
        };
    }
}
