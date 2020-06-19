<?php

require_once __DIR__ . '/VTgObject.php';
require_once __DIR__ . '/VTgMessage.php';
require_once __DIR__ . '/VTgUser.php';
require_once __DIR__ . '/../VTgMetaObjects/VTgAction.php';

/**
 * @brief Class to represent user callback query
 * @details Callback queries are usually send when push buttons on inline keyboard
 */
class VTgCallbackQuery extends VTgObject
{
    /**
     * @var int|string $id
     * @brief Unique identifier of query (used for trigger an answer by e. g. VTgRequestor::answerCallbackQuery())
     */
    public $id;

    /**
     * @var VTgUser $from
     * @brief User who initiated a query
     */
    public $from;

    /**
     * @var VTgMessage|null $message
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
     * @brief Constructor-initializer
     * @param array $data JSON-decoded callback query data received from Telegram
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->from = new VTgUser($data['from']);
        $this->data = $data['data'];
        $this->message = isset($data['message']) ? new VTgMessage($data['message']) : null;
        if (isset($data['inline_message_id'])) {
            $this->fromInlineMode = true;
            $this->inlineMessageId = $data['inline_message_id'];
        } else {
            $this->fromInlineMode = false;
        }
    }

    /**
     * @brief Creates an action to answer this callback query
     * @param array $extraParameters Extra parameters for request if needed
     * @return VTgAction "Answer callback query" action (ready to execute)
     */
    public function answer(array $extraParameters = []): VTgAction
    {
        return VTgAction::answerCallbackQuery($this->id, $extraParameters);
    }

    /**
     * @brief Creates an action to answer this callback query with text and alert if needed
     * @param string $text Text of the notification
     * @param bool $showAlert If true, an alert will be shown by the client instead of a notification at the top of the chat screen
     * @return VTgAction "Answer callback query" action (ready to execute)
     */
    public function answerWithText(string $text, bool $showAlert = false): VTgAction
    {
        return $this->answer([
            'text' => $text,
            'show_alert' => $showAlert
        ]);
    }

    /**
     * @brief Creates an action to edit text of the message callback query attached to
     * @param string $text Text of the message
     * @return VTgAction "Edit message text" or "edit inline message text" action (ready to execute)
     */
    public function editMessageText(string $text, array $extraParameters = []): VTgAction
    {
        if ($this->fromInlineMode)
            return VTgAction::editIMessageText($this->inlineMessageId, $text, $extraParameters);
        return VTgAction::editMessageText($this->message->chat->id, $this->message->id, $text, $extraParameters);
    }

    /**
     * @brief Creates an action to edit reply markup of the message callback query attached to
     * @param mixed $replyMarkup New reply markup or false to remove
     * @return VTgAction "Edit message reply markup" or "edit inline message reply markup" action (ready to execute)
     */
    public function editMessageReplyMarkup($replyMarkup = false): VTgAction
    {
        if ($this->fromInlineMode)
            return VTgAction::editIMessageReplyMarkup($this->inlineMessageId, $replyMarkup);
        return VTgAction::editMessageReplyMarkup($this->message->chat->id, $this->message->id, $replyMarkup);
    }
}
