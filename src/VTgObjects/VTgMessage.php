<?php

require_once VTELEGRAM_REQUIRE_DIR . '/VTgObjects/VTgObject.php';
require_once VTELEGRAM_REQUIRE_DIR . '/VTgObjects/VTgUser.php';
require_once VTELEGRAM_REQUIRE_DIR . '/VTgObjects/VTgChat.php';
require_once VTELEGRAM_REQUIRE_DIR . '/VTgObjects/VTgMessageEntity.php';

/**
 * @brief Class represents a message in Telegram
 * @todo Types for untyped member properties
 */
class VTgMessage extends VTgObject
{
    public $id;
    public $from = null;
    public $date;
    public $chat;
    public $forwardFrom = null;
    public $forwardDate = null;
    public $replyTo = null;
    public $text = "";
    public $entities = null;
    public $audio = null;
    public $document = null;
    public $photo = [];
    public $sticker = null;
    public $video = null;
    public $voice = null;
    public $caption = null;
    public $contact = null;
    public $location = null;
    public $venue = null;
    public $service = 0;
    public $serviceData = null;

    const SERVICE__UNDEFINED = 0;

    public function __construct(array $data)
    {
        $this->id = $data['message_id'] ?? $data['id'] ?? 0;
        $this->date = new DateTime('@' . $data['date']);
        $this->chat = new VTgChat($data['chat']);
        $this->from = isset($data['from']) ? new VTgUser($data['from']) : null;
        $this->forwardFrom = isset($data['forward_from']) ? new VTgUser($data['forward_from']) : null;
        $this->forwardDate = isset($data['forward_date']) ? new DateTime('@' . $data['forward_date']) : null;
        $this->replyTo = isset($data['reply_to_message']) ? new VTgMessage($data['reply_to_message']) : null;
        $this->text = $data['text'] ?? "";
        if(isset($data['entities'])) {
            $this->entities = [];
            foreach($data['entities'] as $entity)
                $this->entities[] = new VTgMessageEntity($entity);
        }
    }
}