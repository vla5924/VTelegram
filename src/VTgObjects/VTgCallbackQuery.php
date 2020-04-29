<?php

require_once VTELEGRAM_REQUIRE_DIR . '/VTgObjects/VTgObject.php';
require_once VTELEGRAM_REQUIRE_DIR . '/VTgObjects/VTgMessage.php';
require_once VTELEGRAM_REQUIRE_DIR . '/VTgObjects/VTgUser.php';

class VTgCallbackQuery extends VTgObject
{
    public string $id;
    public VTgUser $from;
    public VTgMessage $message = null;
    public bool $fromInlineMode = false;
    public string $inlineMessageId = null;
    public string $data = "";

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