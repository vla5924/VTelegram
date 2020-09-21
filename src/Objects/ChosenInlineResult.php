<?php

namespace VTg\Objects;

use VTg\Objects\BaseObject;
use VTg\Objects\Handlable;
use VTg\Objects\User;
use VTg\MetaObjects\Action;

/**
 * @brief Class to represent chosen inline result
 */
class ChosenInlineResult extends BaseObject implements Handlable
{
    /**
     * @var string $id
     * @brief Unique identifier for the result that was chosen
     */
    public $id;

    /**
     * @var User $from
     * @brief The user that chose the result
     */
    public $from;

    /**
     * @var Location|null $location
     * @brief Sender location, only for bots that require user location
     */
    public $location = null;

    /**
     * @var string|null $inlineMessageId
     * @brief  Identifier of the sent inline message
     * @details Available only if there is an inline keyboard attached to the message. 
     * Will be also received in callback queries and can be used to edit the message.
     */
    public $inlineMessageId = null;

    /**
     * @var string $query
     * @brief The query that was used to obtain the result
     */
    public $query;

    /**
     * @brief Constructor-initializer
     * @param array $data JSON-decoded chosen inline result data received from Telegram
     */
    public function __construct(array $data)
    {
        $this->id = $data["result_id"];
        $this->from = new User($data["from"]);
        $this->inlineMessageId = $data["inline_message_id"] ?? null;
        $this->query = $data["query"];
    }

    /**
     * @brief Returns chosen inline result ID
     * @return string Result unique identifier
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @brief Returns result choosing instigator (user that chose the result)
     * @return User Result choosing instigator
     */
    public function getInstigator(): User
    {
        return $this->from;
    }

    /**
     * @brief Returns class name
     * @return string "\VTg\Objects\ChosenInlineResult"
     */
    public function getClass(): string
    {
        return "\VTg\Objects\ChosenInlineResult";
    }

    /**
     * @brief Returns Telegram type name
     * @return string "chosen_inline_result"
     */
    public function getType(): string
    {
        return "chosen_inline_result";
    }

    /**
     * @brief Creates an action to edit message text that was sent by choosing this result
     * @param string $text New message text
     * @param array $extraParameters Other parameters if needed
     * @return Action|null "Edit inline message text" action if $inlineMessageId present, null otherwise
     */
    public function editMessageText(string $text, array $extraParameters = []): ?Action
    {
        return $this->inlineMessageId ? Action::editIMessageText($this->inlineMessageId, $text, $extraParameters) : null;
    }

    /**
     * @brief Creates an action to edit reply markup of message that was sent by choosing this result
     * @param string|false $replyMarkup New reply markup
     * @return Action|null "Edit inline message reply markup" action if $inlineMessageId present, null otherwise
     */
    public function editMessageReplyMarkup($replyMarkup = false): ?Action
    {
        return $this->inlineMessageId ? Action::editIMessageReplyMarkup($this->inlineMessageId, $replyMarkup) : null;
    }
}
