<?php

require_once __DIR__ . '/VTgRequestor.php';
require_once __DIR__ . '/VTgMetaObjects/VTgAction.php';
require_once __DIR__ . '/VTgObjects/VTgUpdate.php';
require_once __DIR__ . '/VTgObjects/VTgCallbackQuery.php';
require_once __DIR__ . '/VTgObjects/VTgMessage.php';
require_once __DIR__ . '/VTgBotController.php';

/**
 * @brief Complex solution for creating a Telegram bot
 * @details Class provides a complex solution for creating a Telegram bot, including handling for commands, 
 * callback queries, inline mode, etc. It is recommended to create a child class for extending your own bot
 * with special functions (e.g. authenticating, API calls to other services, some computing etc.)
 * @todo Handlers for inline mode (chosen result), processing with media & captions, sending files, error messages (e.g. command not found)
 */
class VTgBot
{
    /**
     * @var VTgRequestor|null $tg
     * @brief VTgRequestor instance for accessing Bot API
     * @details If you want to use it in your methods, extend this class using inheritance
     */
    static protected $tg = null;

    /**
     * @var array $commands
     * @brief Array with commands handlers
     * @details Each handler (being a callable type) must have a special header format, see registerCommandHandler()
     */
    static protected $commands = [];

    /**
     * @var callable|null $commandFallbackHandler
     * @brief Function for handling messages if they don't contain /commands
     * @details Handler must have a special header format, see registerCommandFallbackHandler()
     */
    static protected $commandFallbackHandler = null;

    /**
     * @var callable|null $standardMessageHadler
     * @brief Function for handling messages if they don't contain /commands
     * @details Handler must have a special header format, see registerStandardMessageHandler()
     */
    static protected $standardMessageHadler = null;

    /**
     * @var callable|null $callbackQueryHandler
     * @brief Function for handling callback queries if needed
     * @details Handler must have a special header format, see registerCallbackQueryHandler()
     */
    static protected $callbackQueryHandler = null;

    /**
     * @var callable|null $inlineQueryHandler
     * @brief Function for handling inline queries if needed
     * @details Handler must have a special header format, see registerInlineQueryHandler()
     */
    static protected $inlineQueryHandler = null;

    /**
     * @var callable|null $inlineResultHandler
     * @brief Function for handling chosen inline results if needed
     * @details Handler must have a special header format, see registerInlineResultHandler()
     */
    static protected $inlineResultHandler = null;

    /**
     * @brief Constructs static VTgRequestor instance if needed
     */
    static protected function setUpRequestor(): void
    {
        if (!static::$tg)
            static::$tg = new VTgRequestor();
    }

    /**
     * @brief Updates stored Bot API token for VTgRequestor instance
     * @param string $token New Bot API token
     */
    static public function setToken(string $token): void
    {
        static::setUpRequestor();
        static::$tg->updateToken($token);
    }

    /**
     * @brief Enables SOCKS5 proxy for VTgRequestor instance
     * @details Requests to Bot API will be made via proxy server
     * @param string $address Proxy HTTP address
     * @param string $port Connection port
     * @param string $username Username for proxy authentication
     * @param string $password Password for proxy authentication
     */
    static public function enableProxy(string $address, string $port, string $username, string $password): void
    {
        static::setUpRequestor();
        static::$tg->enableProxy($address, $port, $username, $password);
    }

    /**
     * @brief Disables SOCKS5 proxy for VTgRequestor instance if enabled
     */
    static public function disableProxy(): void
    {
        static::setUpRequestor();
        static::$tg->disableProxy();
    }

    /**
     * @brief Makes bot controller object
     * @return VTgBotController Bot controller object
     */
    static protected function makeController(): VTgBotController
    {
        return new VTgBotController(static::$tg);
    }

    /**
     * @brief Registers a function as a regular message handler
     * @details Registers a function to handle with any messages or (if command handlers were set) 
     * with messages without commands. A handler will be passed VTgBotController and
     * VTgMessage object. So you can use it like this:
     * @code
     * VTgBot::registerStandardMessageHandler(function (VTgBotController $bot, VTgMessage $message) {
     *   $answer = 'You sent me: ' . $message->text;
     *   $bot->execute(VTgAction::sendMessage($message->chat->id, $answer));
     * });
     * @endcode
     * @param callable $handler Standard message handler [function (VTgBotController, VTgMessage)]
     */
    static public function registerStandardMessageHandler(callable $handler): void
    {
        static::$standardMessageHadler = $handler;
    }

    /**
     * @brief Registers a function as a handler for messages containing /commands
     * @details A handler will be passed: VTgBotController object as first parameter,
     * VTgMessage object as second parameter,  string (part of message following the 
     * command) as third parameter. So you can use it like this:
     * @code
     * VTgBot::registerCommandHandler('hello', function (VTgBotController $bot, VTgMessage $message, string $data) {
     *   $answer = 'Hello, ' . $message->chat->id;
     *   $bot->execute(VTgAction::sendMessage($message->chat->id, $answer));
     * });
     * @endcode
     * @param string $command Command you want to handle (don't mention '/', e.g. 'help', not '/help')
     * @param callable $handler Command handler [function (VTgBotController, VTgMessage, string)]
     */
    static public function registerCommandHandler(string $command, callable $handler): void
    {
        static::$commands[$command] = $handler;
    }

    /**
     * @brief Registers a function as a handler for messages containing undefined /commands
     * @details A handler will be passed: VTgBotController object as first parameter,
     * VTgMessage object as second parameter,  string with command as third parameter,
     * string (part of message following the command) as fourth parameter.
     * So you can use it like this:
     * @code
     * VTgBot::registerCommandFallbackHandler(function (VTgBotController $bot, VTgMessage $message, string $command, string $data) {
     *   $bot->execute(VTgAction::sendMessage($message->chat->id, "I don't know this command: " . $command));
     * });
     * @endcode
     * @param callable $handler Command handler [function (VTgBotController, VTgMessage, string)]
     */
    static public function registerCommandFallbackHandler(callable $handler): void
    {
        static::$commandFallbackHandler = $handler;
    }

    /**
     * @brief Registers a function as a callback query handler
     * @details A handler will be passed VTgBotController object and VTgCallbackQuery object.
     * So you can use it like this:
     * @code
     * VTgBot::registerCallbackQueryHandler(function (VTgBotController $bot, VTgCallbackQuery $callbackQuery) {
     *   $newText = 'Callback data: ' . $callbackQuery->data;
     *   $bot->execute(VTgAction::editMessageText($callbackQuery->message->chat->id, $callbackQuery->message->id, $newText));
     * });
     * @endcode
     * @param callable $handler Callback query handler [function (VTgBotController, VTgCallbackQuery)]
     */
    static public function registerCallbackQueryHandler(callable $handler): void
    {
        static::$callbackQueryHandler = $handler;
    }

    /**
     * @brief Registers a function as a inline query handler
     * @details A handler will be passed VTgBotController object and VTgInlineQuery object.
     * @param callable $handler Inline query handler [function (VTgBotController, VTgInlineQuery)]
     */
    static public function registerInlineQueryHandler(callable $handler): void
    {
        static::$inlineQueryHandler = $handler;
    }

    /**
     * @brief Registers a function as a chosen inline result handler
     * @details A handler will be passed VTgBotController object and VTgChosenInlineResult object.
     * @param callable $handler Inline result handler [function (VTgBotController, VTgChosenInlineResult)]
     */
    static public function registerInlineResultHandler(callable $handler): void
    {
        static::$inlineResultHandler = $handler;
    }

    /**
     * @brief Processes JSON-decoded update data from Telegram
     * @details You should pass an associative array with data received from Telegram (with Webhook or Long-poll).
     * For example, you can use it like this:
     * @code
     * $json = file_get_contents('php://input');
     * $data = json_decode($json, true);
     * VTgBot::processUpdateData($data);
     * @endcode
     * Or just use processUpdatePost() to achieve the same behavior.
     * @param array $data Array with JSON-decoded update data
     */
    static public function processUpdateData(array $data): void
    {
        $update = new VTgUpdate($data);
        self::processUpdate($update);
    }

    /**
     * @brief Processes JSON-decoded update data received from Telegram in POST query
     * @details It simply wraps processUpdateData(), getting data from incoming POST query.
     */
    static public function processUpdatePost(): void
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        self::processUpdateData($data);
    }

    /**
     * @brief Processes an update from Telegram
     * @param VTgUpdate $update Object with data received from Telegram
     */
    static public function processUpdate(VTgUpdate $update): void
    {
        static::setUpRequestor();
        if ($update->type == VTgUpdate::TYPE__MESSAGE)
            static::handleMessage($update->message);
        if ($update->type == VTgUpdate::TYPE__CALLBACK_QUERY)
            static::handleCallbackQuery($update->callbackQuery);
        if ($update->type == VTgUpdate::TYPE__INLINE_QUERY)
            static::handleInlineQuery($update->inlineQuery);
        if ($update->type == VTgUpdate::TYPE__CHOSEN_INLINE_RESULT)
            static::handleInlineResult($update->chosenInlineResult);
    }

    /**
     * @brief Handles message with standard message handler if defined
     * @param VTgMessage $message Message received from Telegram user
     */
    static protected function handleMessageStandardly(VTgMessage $message): void
    {
        if (static::$standardMessageHadler) {
            (static::$standardMessageHadler)(self::makeController(), $message);
        }
    }

    /**
     * @brief Hadles with a received message
     * @param VTgMessage $message Message data received from Telegram
     */
    static protected function handleMessage(VTgMessage $message): void
    {
        $containsCommand = ($message->text and $message->text[0] == '/');
        if ($containsCommand && !empty(static::$commands)) {
            $data = explode(' ', $message->text, 2);
            $command = substr($data[0], 1);
            static::handleCommand($message, $command, $data[1] ?? "");
        } else {
            static::handleMessageStandardly($message);
        }
    }

    /**
     * @brief Hadles with a command if found in message
     * @param VTgMessage $message Message data received from Telegram
     * @param string $command Command name to handle
     * @param string $data A part of message following the command
     */
    static protected function handleCommand(VTgMessage $message, string $command, string $data = ""): void
    {
        if (isset(static::$commands[$command])) {
            (static::$commands[$command])(self::makeController(), $message, $data);
        } elseif (static::$commandFallbackHandler) {
            (static::$commandFallbackHandler)(self::makeController(), $message, $command, $data);
        } else {
            static::handleMessageStandardly($message);
        }
    }

    /**
     * @brief Hadles with a callback query
     * @param VTgCallbackQuery $callbackQuery Callback query data received from Telegram
     */
    static protected function handleCallbackQuery(VTgCallbackQuery $callbackQuery): void
    {
        if (self::$callbackQueryHandler) {
            (self::$callbackQueryHandler)(self::makeController(), $callbackQuery);
        }
    }

    /**
     * @brief Hadles with an inline query
     * @param VTgInlineQuery $inlineQuery Inline query data received from Telegram
     */
    static protected function handleInlineQuery(VTgInlineQuery $inlineQuery): void
    {
        if (self::$inlineQueryHandler) {
            (self::$inlineQueryHandler)(self::makeController(), $inlineQuery);
        }
    }

    /**
     * @brief Hadles with a chosen inline result
     * @param VTgChosenInlineResult $inlineResult Inline result data received from Telegram
     */
    static protected function handleInlineResult(VTgChosenInlineResult $inlineResult): void
    {
        if (self::$inlineResultHandler) {
            (self::$inlineResultHandler)(self::makeController(), $inlineResult);
        }
    }
}
/**
 * @example SimpleBot.php
 * Example of how to create a simple Telegram bot with VTgBot class.
 */
