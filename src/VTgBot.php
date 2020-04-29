<?php

require_once VTELEGRAM_REQUIRE_DIR . '/VTgRequestor.php';
require_once VTELEGRAM_REQUIRE_DIR . '/VTgAction.php';
require_once VTELEGRAM_REQUIRE_DIR . '/VTgObjects/VTgUpdate.php';
require_once VTELEGRAM_REQUIRE_DIR . '/VTgObjects/VTgCallbackQuery.php';
require_once VTELEGRAM_REQUIRE_DIR . '/VTgObjects/VTgMessage.php';

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
     * @var VTgRequestor $tg
     * @brief VTgRequestor instance for accessing Bot API
     * @details If you want to use it in your methods, extend this class using inheritance
     */
    static protected $tg = new VTgRequestor();

    /**
     * @var array $commands
     * @brief Array with commands handlers
     * @details Each handler (being a callable type) must have a special header format, see registerCommandHandler()
     */
    static protected $commands = [];

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
     * @brief Updates stored Bot API token for VTelegram instance
     * @param string $token New Bot API token
     */
    static public final function setToken(string $token): void
    {
        static::$tg->updateToken($token);
    }

    /**
     * @brief Enables SOCKS5 proxy for VTelegram instance
     * @details Requests to Bot API will be made via proxy server
     * @param string $address Proxy HTTP address
     * @param string $port Connection port
     * @param string $username Username for proxy authentication
     * @param string $password Password for proxy authentication
     */
    public function enableProxy(string $address, string $port, string $username, string $password): void
    {
        $this->tg->enableProxy($address, $port, $username, $password);
    }

    /**
     * @brief Disables SOCKS5 proxy for VTelegram instance if enabled
     */
    public function disableProxy(): void
    {
        $this->tg->disableProxy();
    }

    /**
     * @brief Registers a function as a handler for messages containing /commands
     * @details A handler will be passed: VTgMessage object as first parameter, 
     * string (part of message following the command) as second parameter. Then, 
     * it must return VTgAction object. So you can use it like this:
     * @code
     * VTgBot::registerCommandHandler('hello', function (VTgMessage &$message, string $data) {
     *   $answer = 'Hello, ' . $message->chat->id;
     *   return VTgAction::sendMessage($message->chat->id, $answer);
     * });
     * @endcode
     * @param string $command Command you want to handle (don't mention '/', e.g. 'help', not '/help')
     * @param callable $handler Command handler (function(VTgMessage, string):VTgAction)
     */
    static public final function registerCommandHandler(string $command, callable $handler): void
    {
        static::$commands[$command] = $handler;
    }

    /**
     * @brief Registers a function as a regular message handler
     * @details Registers a function to handle with any messages or (if command handlers were set) 
     * with messages without commands. A handler will be passed VTgMessage object. 
     * Then, it must return VTgAction object. So you can use it like this:
     * @code
     * VTgBot::registerStandardMessageHandler(function (VTgMessage &$message) {
     *   $answer = 'You sent me: ' . $message->text;
     *   return VTgAction::sendMessage($message->chat->id, $answer);
     * });
     * @endcode
     * @param callable $handler Standard message handler (function(VTgMessage):VTgAction)
     */
    static public final function registerStandardMessageHandler(callable $handler): void
    {
        self::$standardMessageHadler = $handler;
    }

    /**
     * @brief Registers a function as a callback query handler
     * @details A handler will be passed VTgCallbackQuery object.
     * Then, it must return VTgAction object. So you can use it like this:
     * @code
     * VTgBot::registerCallbackQueryHandler(function (VTgCallbackQuery $callbackQuery) {
     *   $newText = 'Callback data: ' . $callbackQuery->data;
     *   return VTgAction::editMessage($callbackQuery->message->chat->id, $callbackQuery->message->id, $newText);
     * });
     * @endcode
     * @param callable $handler Standard message handler (function(VTgMessage):VTgAction)
     */
    static public final function registerCallbackQueryHandler(callable $handler): void
    {
        self::$callbackQueryHandler = $handler;
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
     * @return mixed|bool Result of update handling or false
     */
    static public final function processUpdateData(array $data)
    {
        $update = new VTgUpdate($data);
        return self::handleUpdate($update);
    }

    /**
     * @brief Processes JSON-decoded update data received from Telegram in POST query
     * @details It simply wraps processUpdateData(), getting data from incoming POST query.
     * @return mixed|bool Result of update handling or false
     */
    static public final function processUpdatePost()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        return self::processUpdateData($data);
    }

    /**
     * @brief Processes an update from Telegram
     * @param VTgUpdate $update Object with data received from Telegram
     * @return mixed|bool Result of update handling or false
     */
    static protected final function handleUpdate(VTgUpdate $update)
    {
        if ($update->type == VTgUpdate::TYPE__MESSAGE)
            return self::handleMessage($update->message);
        if ($update->type == VTgUpdate::TYPE__CALLBACK_QUERY)
            return self::handleCallbackQuery($update->callbackQuery);
        return false;
    }

    /**
     * @brief Hadles with a received message
     * @param VTgMessage $message Message data received from Telegram
     * @return mixed|bool Result of message handling or false
     */
    static protected final function handleMessage(VTgMessage $message)
    {
        $action = VTgAction::doNothing();
        $containsCommand = ($message->text and $message->text[0] == '/');
        if ($containsCommand && !empty(self::$commands)) {
            $data = explode(' ', $message->text, 2);
            $command = substr($data[0], 1);
            $action = self::handleCommand($message, $command, $data[1]);
        } else {
            if (self::$standardMessageHadler)
                $action = (self::$standardMessageHadler)($message);
        }
        return self::doAction($action);
    }

    /**
     * @brief Hadles with a command if found in message
     * @param VTgMessage $message Message data received from Telegram
     * @param string $command Command name to handle
     * @param string $data A part of message following the command
     * @return VTgAction Action for how to handle with command
     */
    static protected final function handleCommand(VTgMessage $message, string $command, string $data = ""): VTgAction
    {
        if (!isset(self::$commands[$command]))
            return VTgAction::doNothing();
        return (self::$commands[$command])($message, $data);
    }


    /**
     * @brief Hadles with a callback query
     * @param VTgCallbackQuery $callbackQuery Callback query data received from Telegram
     * @return VTgAction Action for how to handle with command
     */
    static protected final function handleCallbackQuery(VTgCallbackQuery $callbackQuery)
    {
        $action = VTgAction::doNothing();
        if (self::$callbackQueryHandler)
            $action = (self::$callbackQueryHandler)($callbackQuery);
        return self::doAction($action);
    }

    /**
     * @brief Executes an action
     * @details Algorithm depends on action type: sending or editing a message, calling some function etc.
     * @param VTgAction $action Action to do
     * @return mixed Result of action execution
     * @todo EDIT_REPLY_MARKUP action
     */
    static protected final function doAction(VTgAction $action)
    {
        $data = false;
        switch ($action->action):
            case VTgAction::ACTION__SEND_MESSAGE:
                $data = $this->tg->sendMessage($action->chatId, $action->text, $action->extraParameters);
                break;
            case VTgAction::ACTION__EDIT_MESSAGE:
                $data = $this->tg->editMessage($action->chatId, $action->messageId, $action->text, $action->extraParameters);
                break;
            case VTgAction::ACTION__EDIT_REPLY_MARKUP:
                // do action
                break;
            case VTgAction::ACTION__EDIT_INLINE_MESSAGE:
                $data = $this->tg->editInlineMessage($action->inlineMessageId, $action->text, $action->extraParameters);
                break;
            case VTgAction::ACTION__CALL_FUNCTION:
                $action->callFunctionHandler();
                break;
            case VTgAction::ACTION__MULTIPLE:
                $data = [];
                foreach ($action->actions as $act)
                    $data[] = self::doAction($act);
                break;
        endswitch;
        return $data;
    }
}
/**
 * @example SimpleBot.php
 * Example of how to create a simple Telegram bot with VTgBot class.
 */
