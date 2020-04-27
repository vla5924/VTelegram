<?php

require_once VTELEGRAM_REQUIRE_DIR . '/VTgTypes/VTgMessage.php';
require_once VTELEGRAM_REQUIRE_DIR . '/VTgTypes/VTgUser.php';

class VTgCallbackQuery
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