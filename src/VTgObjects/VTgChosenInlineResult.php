<?php

require_once __DIR__ . '/VTgObject.php';
require_once __DIR__ . '/VTgUser.php';

class VTgChosenInlineResult extends VTgObject
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
}
