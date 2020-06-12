<?php

require_once __DIR__ . '/VTgObject.php';
require_once __DIR__ . '/VTgMessage.php';
require_once __DIR__ . '/VTgUser.php';

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
    public function __construct(array $data) {
        $this->id = $data['id'];
        $this->from = new VTgUser($data['user']);
        $this->data = $data['data'];
        $this->message = isset($data['message']) ? new VTgMessage($data['message']) : null;
        if(isset($data['inline_message_id'])) {
            $this->fromInlineMode = true;
            $this->inlineMessageId = $data['inline_message_id'];
        } else {
            $this->fromInlineMode = false;
        }
    }
}