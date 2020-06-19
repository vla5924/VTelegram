<?php

/**
 * @brief Class to reperesent information about actions related to bot to do
 * @details A union-like structure to store information about actions like send a message, call a function etc.
 * @todo Some other actions (edit caption, answer callback query...)
 */
class VTgAction
{
    /**
     * @var int $action
     * @brief Action code
     * @note "Do nothing" by default.
     */
    public $action = 0;

    /**
     * @var int|string $chatId
     * @brief Chat identifier (integer or string)
     * @details Needed for "Send message" and "Edit message" actions
     */
    public $chatId;

    /**
     * @var int $messageId
     * @brief Message identifier (integer)
     * @details Needed for "Send message" and "Edit message" actions
     */
    public $messageId;

    /**
     * @var string $inlineMessageId
     * @brief Inline mode message identifier (string)
     * @details Needed for "Edit message" action
     */
    public $inlineMessageId;

    /**
     * @var string $callbackQueryId
     * @brief Callback query identifier (string)
     * @details Needed for "Answer callback query" action
     */
    public $callbackQueryId;

    /**
     * @var string $text
     * @brief Message body text
     * @details Needed for "Send message" and "Edit message" actions
     */
    public $text;

    /**
     * @var array $extraParameters
     * @brief Array of parameters for Bot API if needed
     * @details Needed for "Send message" and "Edit message" actions
     */
    public $extraParameters = [];

    /**
     * @var callable|null $handler
     * @brief Handler function
     * @details Needed for "Call function" action
     */
    public $handler = null;

    /**
     * @var array|null $actions
     * @brief Array of actions
     * @details Needed for "Multiple" action (one-by-one execution of actions in array)
     */
    public $actions = null;

    const ACTION__DO_NOTHING = 0; ///< Code for "Do nothing" action
    const ACTION__SEND_MESSAGE = 1; ///< Code for "Send message" action
    const ACTION__EDIT_MESSAGE_TEXT = 2; ///< Code for "Edit text message" action
    const ACTION__EDIT_IMESSAGE_TEXT = 3; ///< Code for "Edit inline message" action
    const ACTION__EDIT_MESSAGE_REPLY_MARKUP = 4; ///< Code for "Edit reply markup of message" action
    const ACTION__EDIT_IMESSAGE_REPLY_MARKUP = 5; ///< Code for "Edit reply markup of inline message" action
    const ACTION__ANSWER_CALLBACK_QUERY = 6; ///< Code for "Answer callback query" action
    const ACTION__CALL_FUNCTION = 99; ///< Code for "Call function" action
    const ACTION__MULTIPLE = 100; ///< Code for "Multiple" action (see multiple())


    /**
     * @brief Constructor-initializer
     * @param int $action Action code (see ACTION__DO_NOTHING, ACTION__SEND_MESSAGE etc.)
     * @param mixed|null $parameters Array of action parameters if needed
     */
    public function __construct(int $action, ...$parameters)
    {
        switch ($action):
            case self::ACTION__SEND_MESSAGE:
                $this->chatId = $parameters[0];
                $this->text = $parameters[1];
                $this->extraParameters = $parameters[2] ?? [];
                break;
            case self::ACTION__EDIT_MESSAGE_TEXT:
                $this->chatId = $parameters[0];
                $this->messageId = $parameters[1];
                $this->text = $parameters[2];
                $this->extraParameters = $parameters[3] ?? [];
                break;
            case self::ACTION__EDIT_IMESSAGE_TEXT:
                $this->inlineMessageId = $parameters[0];
                $this->text = $parameters[1];
                $this->extraParameters = $parameters[2] ?? [];
                break;
            case self::ACTION__EDIT_MESSAGE_REPLY_MARKUP:
                $this->chatId = $parameters[0];
                $this->messageId = $parameters[1];
                $this->extraParameters = ['reply_markup' => $parameters[2]] ?? [];
                break;
            case self::ACTION__EDIT_IMESSAGE_REPLY_MARKUP:
                $this->inlineMessageId = $parameters[0];
                $this->extraParameters = ['reply_markup' => $parameters[1]] ?? [];
                break;
            case self::ACTION__ANSWER_CALLBACK_QUERY:
                $this->callbackQueryId = $parameters[0];
                $this->extraParameters = $parameters[1] ?? [];
                break;
            case self::ACTION__CALL_FUNCTION:
                $this->handler = $parameters[0];
                break;
            case self::ACTION__MULTIPLE:
                foreach ($parameters as $parameter)
                    if ($parameter instanceof self)
                        $this->actions[] = $parameter;
                break;
            case self::ACTION__DO_NOTHING:
            default:
                $action = self::ACTION__DO_NOTHING;
                break;
        endswitch;
        $this->action = $action;
    }

    /**
     * @brief Inititates function call
     * @param mixed|null $args Arguments to be passed to handler function
     * @todo Examples of usage
     */
    public function callFunctionHandler(...$args)
    {
        if ($this->action == self::ACTION__CALL_FUNCTION) {
            ($this->handler)(...$args);
        }
    }

    /**
     * @brief Executes an action
     * @details Algorithm depends on action type: sending or editing a message, calling some function etc.
     * @param VTgRequestor|null $tg Instance for calling Telegram API methods
     * @return mixed Result of action execution
     * @todo EDIT_REPLY_MARKUP action
     */
    public function execute(VTgRequestor $tg = null)
    {
        $data = false;
        switch ($this->action):
            case VTgAction::ACTION__SEND_MESSAGE:
                $data = $tg->sendMessage($this->chatId, $this->text, $this->extraParameters);
                break;
            case VTgAction::ACTION__EDIT_MESSAGE_TEXT:
                $data = $tg->editMessageText($this->chatId, $this->messageId, $this->text, $this->extraParameters);
                break;
            case VTgAction::ACTION__EDIT_MESSAGE_REPLY_MARKUP:
                // TODO: do action
                break;
            case VTgAction::ACTION__EDIT_IMESSAGE_TEXT:
                $data = $tg->editIMessageText($this->inlineMessageId, $this->text, $this->extraParameters);
                break;
            case VTgAction::ACTION__CALL_FUNCTION:
                $this->callFunctionHandler();
                break;
            case VTgAction::ACTION__MULTIPLE:
                $data = [];
                foreach ($this->actions as $action)
                    $data[] = $action->execute($tg);
                break;
        endswitch;
        return $data;
    }

    /**
     * @brief Creates "Do nothing" action
     * @return VTgAction Action
     */
    static public function doNothing(): VTgAction
    {
        return new self(self::ACTION__DO_NOTHING);
    }

    /**
     * @brief Creates "Send message" action
     * @param int|string $chatId Chat identifier
     * @param string $text Message body text
     * @param array $extraParameters Extra parameters for API request if needed
     * @return VTgAction Action
     */
    static public function sendMessage($chatId, string $text, array $extraParameters = []): VTgAction
    {
        return new self(self::ACTION__SEND_MESSAGE, $chatId, $text, $extraParameters);
    }

    /**
     * @brief Creates "Edit text message" action
     * @param int|string $chatId Chat identifier
     * @param int $messageId Message identifier
     * @param string $text New message body text
     * @param array $extraParameters Extra parameters for API request if needed
     * @return VTgAction Action
     */
    static public function editMessageText($chatId, int $messageId, string $text, array $extraParameters = []): VTgAction
    {
        return new self(self::ACTION__EDIT_MESSAGE_TEXT, $chatId, $messageId, $text, $extraParameters);
    }

    /**
     * @brief Creates "Edit inline message" action
     * @param string $inlineMessageId Message identifier
     * @param string $text New message body text
     * @param array $extraParameters Extra parameters for API request if needed
     * @return VTgAction Action
     */
    static public function editIMessageText(string $inlineMessageId, string $text, array $extraParameters = []): VTgAction
    {
        return new self(self::ACTION__EDIT_IMESSAGE_TEXT, $inlineMessageId, $text, $extraParameters);
    }

    /**
     * @brief Creates "Edit reply markup of message" action
     * @param int|string $chatId Chat identifier
     * @param int $messageId Message identifier
     * @param string|false $replyMarkup New reply_markup value
     * @return VTgAction Action
     */
    static public function editMessageReplyMarkup($chatId, int $messageId, $replyMarkup = false): VTgAction
    {
        return new self(self::ACTION__EDIT_MESSAGE_REPLY_MARKUP, $chatId, $messageId, $replyMarkup);
    }

    /**
     * @brief Creates "Edit reply markup of inline message" action
     * @param string $inlineMessageId Inline message identifier
     * @param string $replyMarkup New reply_markup value
     * @return VTgAction Action
     */
    static public function editIMessageReplyMarkup(string $inlineMessageId, string $replyMarkup): VTgAction
    {
        return new self(self::ACTION__EDIT_IMESSAGE_REPLY_MARKUP, $inlineMessageId, $replyMarkup);
    }

    /**
     * @brief Creates "Answer callback query" action
     * @param string $callbackQueryId Callback query identifier
     * @param array $extraParameters Extra parameters for API request if needed
     * @return VTgAction Action
     */
    static public function answerCallbackQuery(string $callbackQueryId, array $extraParameters = []): VTgAction
    {
        return new self(self::ACTION__ANSWER_CALLBACK_QUERY, $callbackQueryId, $extraParameters);
    }

    /**
     * @brief Creates "Call function" action
     * @param callable $handler Handler function
     * @return VTgAction Action
     */
    static public function callFunction(callable $handler): VTgAction
    {
        return new self(self::ACTION__CALL_FUNCTION, $handler);
    }

    /**
     * @brief Creates "Multiple" action
     * @details "Multiple" action is an action with array of actions which must have been executed one-by-one
     * @param VTgAction $actions Actions to be executed
     * @return VTgAction Action
     */
    static public function multiple(VTgAction ...$actions): VTgAction
    {
        return new self(self::ACTION__MULTIPLE, ...$actions);
    }
}
