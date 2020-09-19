<?php

require_once __DIR__ . '/VTgObject.php';
require_once __DIR__ . '/VTgHandlable.php';
require_once __DIR__ . '/VTgUser.php';
require_once __DIR__ . '/../VTgMetaObjects/VTgAction.php';

/**
 * @brief Class to represent chosen inline result
 */
class VTgChosenInlineResult extends VTgObject implements VTgHandlable
{
    public $id;
    public $from;
    public $location = null;
    public $inlineMessageId = null;
    public $query;

    public function __construct(array $data)
    {
        $this->id = $data["result_id"];
        $this->from = new VTgUser($data["from"]);
        $this->inlineMessageId = $data["inline_message_id"] ?? null;
        $this->query = $data["query"];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getInstigator(): VTgUser
    {
        return $this->from;
    }

    public function editMessageText(string $text, array $extraParameters = []): ?VTgAction
    {
        return $this->inlineMessageId ? VTgAction::editIMessageText($this->inlineMessageId, $text, $extraParameters) : null;
    }

    public function editMessageReplyMarkup($replyMarkup = false): ?VTgAction
    {
        return $this->inlineMessageId ? VTgAction::editIMessageReplyMarkup($this->inlineMessageId, $replyMarkup) : null;
    }
}
