<?php

/**
 * @brief Class to reperesents information about actions related to bot to do
 * @details A union-like structure to store information about actions like send a message, call a function etc.
 * @todo Some other actions
 */
class VTgAction
{
    /**
     * @brief Action code
     * @details "Do nothing" by default.
     */
    public int $action = 0;

    /**
     * @brief Chat identifier (integer or string)
     * @details Needed for "Send message" and "Edit message" actions
     */
    public $chatId;

    /**
     * @brief Message identifier (integer)
     * @details Needed for "Send message" and "Edit message" actions
     */
    public $messageId;

    /**
     * @brief Inline mode message identifier (string)
     * @details Needed for "Edit message" actions 
     */
    public $inlineMessageId;

    /**
     * @brief Message body text
     * @details Needed for "Send message" and "Edit message" actions
     */
    public string $text;

    /**
     * @brief Array of parameters for Bot API if needed
     * @details Needed for "Send message" and "Edit message" actions
     */
    public array $extraParameters = [];

    /**
     * @brief Handler function
     * @details Needed for "Call function" action
     */
    public callable $handler = null;

    /**
     * @brief Array of actions
     * @details Needed for "Multiple" action (one-by-one execution of actions in array)
     */
    public array $actions = null;

    const ACTION__DO_NOTHING = 0;          ///< Represents "Do nothing" action
    const ACTION__SEND_MESSAGE = 1;        ///< Represents "Send message" action
    const ACTION__EDIT_MESSAGE = 2;        ///< Represents "Edit message regularly" action
    const ACTION__EDIT_REPLY_MARKUP = 3;   ///< Represents "Edit reply markup of message" action
    const ACTION__EDIT_INLINE_MESSAGE = 4; ///< Represents "Edit inline mode message" action
    const ACTION__CALL_FUNCTION = 100;     ///< Represents "Call function" action
    const ACTION__MULTIPLE = 101;          ///< Represents "Multiple" action (see multiple())


    /**
     * @brief Constructor-initializer
     * @param int $action Action code (see ACTION__DO_NOTHING, ACTION__SEND_MESSAGE etc.)
     * @param mixed|null $parameters Array of action parameters if needed
     */
    public function __construct(int $action, ...$parameters = null)
    {
        $this->action = $action;
        switch ($this->action):
            case self::ACTION__SEND_MESSAGE:
                $this->chatId = $parameters[0];
                $this->text = $parameters[1];
                $this->extraParameters = $parameters[2] ?? [];
                break;
            case self::ACTION__EDIT_MESSAGE:
                $this->chatId = $parameters[0];
                $this->messageId = $parameters[1];
                $this->text = $parameters[2];
                $this->extraParameters = $parameters[3] ?? [];
                break;
            case self::ACTION__EDIT_REPLY_MARKUP:
                $this->chatId = $parameters[0];
                $this->messageId = $parameters[1];
                $this->extraParameters = ['reply_markup' => $parameters[2]];
                break;
            case self::ACTION__EDIT_INLINE_MESSAGE:
                $this->inlineMessageId = $parameters[0];
                $this->text = $parameters[1];
                $this->extraParameters = $parameters[2] ?? [];
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
                break;
        endswitch;
    }

    /**
     * @brief Inititates function call
     * @param mixed|null $args Arguments to be passed to handler function
     * @todo Examples of usage
     */
    public function callFunctionHandler(...$args = null)
    {
        if ($this->action == self::ACTION__CALL_FUNCTION) {
            ($this->handler)(...$args);
        }
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
     * @brief Creates "Edit regular message" action
     * @param int|string $chatId Chat identifier
     * @param int $messageId Message identifier
     * @param string $text New message body text
     * @param array $extraParameters Extra parameters for API request if needed
     * @return VTgAction Action
     */
    static public function editMessage($chatId, int $messageId, string $text, array $extraParameters = []): VTgAction
    {
        return new self(self::ACTION__EDIT_MESSAGE, $chatId, $messageId, $text, $extraParameters);
    }

    /**
     * @brief Creates "Edit reply markup of message" action
     * @param int|string $chatId Chat identifier
     * @param int $messageId Message identifier
     * @param string $replyMarkup New reply_markup value
     * @return VTgAction Action
     */
    static public function editReplyMarkup($chatId, int $messageId, string $replyMarkup): VTgAction
    {
        return new self(self::ACTION__EDIT_REPLY_MARKUP, $chatId, $messageId, $replyMarkup);
    }

    /**
     * @brief Creates "Edit inline mode message" action
     * @param string $inlineMessageId Message identifier
     * @param string $text New message body text
     * @param array $extraParameters Extra parameters for API request if needed
     * @return VTgAction Action
     */
    static public function editInlineMessage(string $inlineMessageId, string $text, array $extraParameters = []): VTgAction
    {
        return new self(self::ACTION__EDIT_INLINE_MESSAGE, $inlineMessageId, $text, $extraParameters);
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
    static public function multiple(VTgAction ...$actions) : VTgAction
    {
        return new self(self::ACTION__MULTIPLE, ...$actions);
    }
}
