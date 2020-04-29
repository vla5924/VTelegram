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
    public int $id;
    public VTgUser $from = null;
    public DateTime $date;
    public VTgChat $chat;
    public VTgUser $forwardFrom = null;
    public DateTime $forwardDate = null;
    public VTgMessage $replyTo = null;
    public string $text = "";
    public array $entities = null;
    public $audio = null;
    public $document = null;
    public array $photo = [];
    public $sticker = null;
    public $video = null;
    public $voice = null;
    public $caption = null;
    public $contact = null;
    public $location = null;
    public $venue = null;
    public int $service = 0;
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