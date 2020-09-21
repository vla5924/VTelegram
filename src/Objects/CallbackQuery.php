<?php

namespace VTg\Objects;

use VTg\Objects\BaseObject;
use VTg\Objects\Handlable;
use VTg\Objects\User;
use VTg\Objects\Message;
use VTg\MetaObjects\Action;

/**
 * @brief Class to represent user callback query
 * @details Callback queries are usually send when push buttons on inline keyboard
 */
class CallbackQuery extends BaseObject implements Handlable
{
    /**
     * @var int|string $id
     * @brief Unique identifier of query (used for trigger an answer by e. g. VTgRequestor::answerCallbackQuery())
     */
    public $id;

    /**
     * @var User $from
     * @brief User who initiated a query
     */
    public $from;

    /**
     * @var Message|null $message
     * @brief Message
     */
    public $message = null;

    /**
     * @var bool $fromInlineMode
     * @brief Flag if query was initiated from inline mode
     */
    public $fromInlineMode = false;

    /**
     * @var string|null $inlineMessageId
     * @brief Unique identifier of inline message
     */
    public $inlineMessageId = null;

    /**
     * @var string $data
     * @brief Callback query data (payload for bot)
     */
    public $data = "";

    /**
     * @var string $chatInstance
     * @brief Global identifier, uniquely corresponding to the chat to which the message with the callback button was sent
     */
    public $chatInstance;

    /**
     * @brief Constructor-initializer
     * @param array $data JSON-decoded callback query data received from Telegram
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->from = new User($data['from']);
        $this->data = $data['data'];
        $this->message = isset($data['message']) ? new Message($data['message']) : null;
        if (isset($data['inline_message_id'])) {
            $this->fromInlineMode = true;
            $this->inlineMessageId = $data['inline_message_id'];
        } else {
            $this->fromInlineMode = false;
        }
        $this->chatInstance = $data['chat_instance'];
    }

    /**
     * @brief Returns callback query ID
     * @return int|string Query unique identifier
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @brief Returns query instigator (user who initiated this query)
     * @return User Query instigator
     */
    public function getInstigator(): User
    {
        return $this->from;
    }

    /**
     * @brief Returns class name
     * @return string "\VTg\Objects\CallbackQuery"
     */
    public function getClass(): string
    {
        return "\VTg\Objects\CallbackQuery";
    }

    /**
     * @brief Returns Telegram type name
     * @return string "callback_query"
     */
    public function getType(): string
    {
        return "callback_query";
    }

    /**
     * @brief Creates an action to answer this callback query
     * @param array $extraParameters Extra parameters for request if needed
     * @return Action "Answer callback query" action (ready to execute)
     */
    public function answer(array $extraParameters = []): Action
    {
        return Action::answerCallbackQuery($this->id, $extraParameters);
    }

    /**
     * @brief Creates an action to answer this callback query with text and alert if needed
     * @param string $text Text of the notification
     * @param bool $showAlert If true, an alert will be shown by the client instead of a notification at the top of the chat screen
     * @return Action "Answer callback query" action (ready to execute)
     */
    public function answerWithText(string $text, bool $showAlert = false): Action
    {
        return $this->answer([
            'text' => $text,
            'show_alert' => $showAlert
        ]);
    }

    /**
     * @brief Creates an action to edit text of the message callback query attached to
     * @param string $text Text of the message
     * @return Action "Edit message text" or "edit inline message text" action (ready to execute)
     */
    public function editMessageText(string $text, array $extraParameters = []): Action
    {
        if ($this->fromInlineMode)
            return Action::editIMessageText($this->inlineMessageId, $text, $extraParameters);
        return Action::editMessageText($this->message->chat->id, $this->message->id, $text, $extraParameters);
    }

    /**
     * @brief Creates an action to edit reply markup of the message callback query attached to
     * @param mixed $replyMarkup New reply markup or false to remove
     * @return Action "Edit message reply markup" or "edit inline message reply markup" action (ready to execute)
     */
    public function editMessageReplyMarkup($replyMarkup = false): Action
    {
        if ($this->fromInlineMode)
            return Action::editIMessageReplyMarkup($this->inlineMessageId, $replyMarkup);
        return Action::editMessageReplyMarkup($this->message->chat->id, $this->message->id, $replyMarkup);
    }
}
