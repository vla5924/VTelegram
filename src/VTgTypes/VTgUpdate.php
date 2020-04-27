<?php

require_once VTELEGRAM_REQUIRE_DIR . '/VTgTypes/VTgMessage.php';

class VTgUpdate
{
    public int $id;
    public int $type;
    public VTgMessage $message = null;
    public $inlineQuery = null;
    public $chosenInlineResult = null;
    public $callbackQuery = null;

    const TYPE__UNKNOWN = 0;
    const TYPE__MESSAGE = 1;
    const TYPE__INLINE_QUERY = 2;
    const TYPE__CHOSEN_INLINE_RESULT = 3;
    const TYPE__CALLBACK_QUERY = 4;

    public function __construct(array $data) {
        $this->id = $data['update_id'];
        $this->message = isset($data['message']) ? new VTgMessage($data['message']) : null;
    }
}