<?php

namespace VTg;

use VTg\Requestor;
use VTg\BotController;
use VTg\MetaObjects\Error;
use VTg\Objects\Update;
use VTg\Objects\Handlable;
use VTg\Objects\ChosenInlineResult;
use VTg\Objects\CallbackQuery;
use VTg\Objects\InlineQuery;
use VTg\Objects\Message;


/**
 * @brief Complex solution for creating a Telegram bot
 * @details Class provides a complex solution for creating a Telegram bot, including handling for commands, 
 * callback queries, inline mode, etc. It is recommended to create a child class for extending your own bot
 * with special functions (e.g. authenticating, API calls to other services, some computing etc.)
 * @todo Handlers for inline mode (chosen result), processing with media & captions, sending files, error messages (e.g. command not found)
 */
class Bot
{
    /**
     * @var Requestor|null $tg
     * @brief VTgRequestor instance for accessing Bot API
     * @details If you want to use it in your methods, extend this class using inheritance
     */
    protected static $tg = null;

    /**
     * @var callable|null $preHandler
     * @brief Function for pre-handling (called before any other hander and can pass additional data for them)
     * @details Handler must have a special header format, see registerPreHandler()
     */
    protected static $preHandler = null;

    /**
     * @var array $commands
     * @brief Array with commands handlers
     * @details Each handler (being a callable type) must have a special header format, see registerCommandHandler()
     */
    protected static $commands = [];

    /**
     * @var callable|null $commandFallbackHandler
     * @brief Function for handling messages if they don't contain /commands
     * @details Handler must have a special header format, see registerCommandFallbackHandler()
     */
    protected static $commandFallbackHandler = null;

    /**
     * @var callable|null $standardMessageHadler
     * @brief Function for handling messages if they don't contain /commands
     * @details Handler must have a special header format, see registerStandardMessageHandler()
     */
    protected static $standardMessageHadler = null;

    /**
     * @var callable|null $callbackQueryHandler
     * @brief Function for handling callback queries if needed
     * @details Handler must have a special header format, see registerCallbackQueryHandler()
     */
    protected static $callbackQueryHandler = null;

    /**
     * @var callable|null $inlineQueryHandler
     * @brief Function for handling inline queries if needed
     * @details Handler must have a special header format, see registerInlineQueryHandler()
     */
    protected static $inlineQueryHandler = null;

    /**
     * @var callable|null $inlineResultHandler
     * @brief Function for handling chosen inline results if needed
     * @details Handler must have a special header format, see registerInlineResultHandler()
     */
    protected static $inlineResultHandler = null;

    /**
     * @brief Constructs static Requestor instance if needed
     */
    protected static function setUpRequestor(): void
    {
        if (!static::$tg)
            static::$tg = new Requestor();
    }

    /**
     * @brief Updates stored Bot API token for VTgRequestor instance
     * @param string $token New Bot API token
     */
    public static function setToken(string $token): void
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
    public static function enableProxy(string $address, string $port, string $username, string $password): void
    {
        static::setUpRequestor();
        static::$tg->enableProxy($address, $port, $username, $password);
    }

    /**
     * @brief Disables SOCKS5 proxy for VTgRequestor instance if enabled
     */
    public static function disableProxy(): void
    {
        static::setUpRequestor();
        static::$tg->disableProxy();
    }

    /**
     * @brief Updates default parse mode
     * @details Changes default parse mode to passed so it will be used when sending and editing messages
     * @param string $parseMode New default parse mode name
     */
    public static function setParseMode(string $parseMode): void
    {
        static::$tg->setParseMode($parseMode);
    }

    /**
     * @brief Updates default disabling web page preview state
     * @param bool $value Parameter value
     */
    public static function setDisableWebPagePreview(bool $value = true): void
    {
        static::$tg->setDisableWebPagePreview($value);
    }

    /**
     * @brief Adds or changes default parameter
     * @param string $name Parameter name
     * @param mixed $value Parameter value
     */
    public function setDefaultParameter(string $name, $value = true): void
    {
        static::$tg->setDefaultParameter($name, $value);
    }

    /**
     * @brief Removes default parameter if set
     * @param string $name Parameter name
     */
    public function unsetDefaultParameter(string $name): void
    {
        static::$tg->unsetDefaultParameter($name);
    }

    /**
     * @brief Calls pre-handler and makes bot controller object for further handling
     * @return VTgBotController Bot controller object
     */
    protected static function makeController(Handlable $object): BotController
    {
        $basicController = new BotController(static::$tg);
        if (static::$preHandler)
            return new BotController(static::$tg, (static::$preHandler)($basicController, $object));
        return $basicController;
    }

    /**
     * @brief Registers a function as a pre-handler
     * @details Registers a function to handle with object before any handler. 
     * A pre-handler will be passed VtgBotController (with only $tg property) 
     * and VTgHandlable object and its return value will be added to 
     * $preHandled property of VTgBotController for further handlers.
     * @code
     * VTgBot::registerPreHandler(function (VTgBotController $bot, VTgHandlable $object) {
     *   $user = $myDbController->authorizeUser($object->getInstigator()->id);
     *   return $user;
     * });
     * @endcode
     * @param callable $handler Pre-handler [function (VTgBotController, VTgHandlable)]
     */
    public static function registerPreHandler(callable $handler): void
    {
        static::$preHandler = $handler;
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
    public static function registerStandardMessageHandler(callable $handler): void
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
    public static function registerCommandHandler(string $command, callable $handler): void
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
    public static function registerCommandFallbackHandler(callable $handler): void
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
    public static function registerCallbackQueryHandler(callable $handler): void
    {
        static::$callbackQueryHandler = $handler;
    }

    /**
     * @brief Registers a function as a inline query handler
     * @details A handler will be passed VTgBotController object and VTgInlineQuery object.
     * @param callable $handler Inline query handler [function (VTgBotController, VTgInlineQuery)]
     */
    public static function registerInlineQueryHandler(callable $handler): void
    {
        static::$inlineQueryHandler = $handler;
    }

    /**
     * @brief Registers a function as a chosen inline result handler
     * @details A handler will be passed VTgBotController object and VTgChosenInlineResult object.
     * @param callable $handler Inline result handler [function (VTgBotController, VTgChosenInlineResult)]
     */
    public static function registerInlineResultHandler(callable $handler): void
    {
        static::$inlineResultHandler = $handler;
    }

    /**
     * @brief Starts polling to Telegram and wait for updates to handle them synchroniously
     * @param int $timeout Timeout in seconds for long polling (positive number, e. g. 20)
     * @param int $limit Limits the number of updates to be retrieved (values between 1-100 are accepted)
     * @param array $allowedUpdates Array of strings - names of types of updates you want to receive (if needed)
     */
    public static function startPolling(int $timeout, int $limit = 100, array $allowedUpdates = []): Error
    {
        $parameters = [
            'limit' => $limit,
            'timeout' => $timeout
        ];
        if ($allowedUpdates)
            $parameters['allowed_updates'] = json_encode($allowedUpdates);
        $result = static::$tg->getUpdates($parameters);
        $parameters['offset'] = 0;
        do {
            if ($result->ok) {
                foreach ($result->object->array as $update) {
                    static::processUpdate($update);
                    if ($update->id >= $parameters['offset'])
                    $parameters['offset'] = $update->id + 1;
                }
            } else {
                return $result->error;
            }
            $result = static::$tg->getUpdates($parameters);
        } while (true);
    }

    /**
     * @brief Processes JSON-decoded update data received from Telegram in POST query
     * @details It simply wraps processUpdateData(), getting data from incoming POST query.
     */
    public static function processUpdatePost(): void
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        self::processUpdateData($data);
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
    public static function processUpdateData(array $data): void
    {
        $update = new Update($data);
        self::processUpdate($update);
    }

    /**
     * @brief Processes an update from Telegram
     * @param VTgUpdate $update Object with data received from Telegram
     */
    public static function processUpdate(Update $update): void
    {
        static::setUpRequestor();
        if ($update->type == Update::TYPE__MESSAGE)
            static::handleMessage($update->message);
        if ($update->type == Update::TYPE__CALLBACK_QUERY)
            static::handleCallbackQuery($update->callbackQuery);
        if ($update->type == Update::TYPE__INLINE_QUERY)
            static::handleInlineQuery($update->inlineQuery);
        if ($update->type == Update::TYPE__CHOSEN_INLINE_RESULT)
            static::handleInlineResult($update->chosenInlineResult);
    }

    /**
     * @brief Handles message with standard message handler if defined
     * @param VTgMessage $message Message received from Telegram user
     */
    protected static function handleMessageStandardly(Message $message): void
    {
        if (static::$standardMessageHadler) {
            (static::$standardMessageHadler)(self::makeController($message), $message);
        }
    }

    /**
     * @brief Hadles with a received message
     * @param VTgMessage $message Message data received from Telegram
     */
    protected static function handleMessage(Message $message): void
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
    protected static function handleCommand(Message $message, string $command, string $data = ""): void
    {
        if (isset(static::$commands[$command])) {
            (static::$commands[$command])(self::makeController($message), $message, $data);
        } elseif (static::$commandFallbackHandler) {
            (static::$commandFallbackHandler)(self::makeController($message), $message, $command, $data);
        } else {
            static::handleMessageStandardly($message);
        }
    }

    /**
     * @brief Hadles with a callback query
     * @param VTgCallbackQuery $callbackQuery Callback query data received from Telegram
     */
    protected static function handleCallbackQuery(CallbackQuery $callbackQuery): void
    {
        if (self::$callbackQueryHandler) {
            (self::$callbackQueryHandler)(self::makeController($callbackQuery), $callbackQuery);
        }
    }

    /**
     * @brief Hadles with an inline query
     * @param VTgInlineQuery $inlineQuery Inline query data received from Telegram
     */
    protected static function handleInlineQuery(InlineQuery $inlineQuery): void
    {
        if (self::$inlineQueryHandler) {
            (self::$inlineQueryHandler)(self::makeController($inlineQuery), $inlineQuery);
        }
    }

    /**
     * @brief Hadles with a chosen inline result
     * @param VTgChosenInlineResult $inlineResult Inline result data received from Telegram
     */
    protected static function handleInlineResult(ChosenInlineResult $inlineResult): void
    {
        if (self::$inlineResultHandler) {
            (self::$inlineResultHandler)(self::makeController($inlineResult), $inlineResult);
        }
    }
}
/**
 * @example SimpleBot.php
 * Example of how to create a simple Telegram bot with VTgBot class.
 */
