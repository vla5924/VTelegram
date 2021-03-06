<?php

namespace VTg\Objects;

use VTg\Objects\BaseObject;
use VTg\Objects\Handlable;
use VTg\Objects\Chat;
use VTg\Objects\User;
use VTg\Objects\MessageEntity;
use VTg\MetaObjects\Action;

/**
 * @brief Class represents a message in Telegram
 * @todo Types for untyped member properties
 */
class Message extends BaseObject implements Handlable
{
    /**
     * @var int $id
     * @brief Message unique identifier
     */
    public $id;

    /**
     * @var User|null $from
     * @brief Sender, empty for messages sent to channels
     */
    public $from = null;

    /**
     * @var DateTime $date
     * @brief Date the message was sent
     */
    public $date;

    /**
     * @var Chat $chat
     * @brief Conversation the message belongs to
     */
    public $chat;

    /**
     * @var User|null $forwardFrom
     * @brief For forwarded messages, sender of the original message
     */
    public $forwardFrom = null;

    /**
     * @var DateTime|null $forwardDate
     * @brief For forwarded messages, date the original message was sent
     */
    public $forwardDate = null;

    /**
     * @var Message|null $replyTo
     * @brief For replies, the original message
     */
    public $replyTo = null;

    /**
     * @var string $text
     * @brief Message text
     */
    public $text = "";

    /**
     * @var array|null $entities
     * @brief Array of MessageEntity (if appeared in the text)
     */
    public $entities = null;

    public $replyMarkup = null;
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
    const SERVICE__NEW_CHAT_MEMBERS = 1;
    const SERVICE__LEFT_CHAT_MEMBER = 2;
    const SERVICE__NEW_CHAT_TITLE = 3;
    const SERVICE__NEW_CHAT_PHOTO = 4;
    const SERVICE__DELETE_CHAT_PHOTO = 5;
    const SERVICE__GROUP_CHAT_CREATED = 6;
    const SERVICE__SUPERGROUP_CHAT_CREATED = 7;
    const SERVICE__CHANNEL_CHAT_CREATED = 8;
    const SERVICE__PINNED_MESSAGE = 9;
    const SERVICE__INVOICE = 10;
    const SERVICE__SUCCESSFUL_PAYMENT = 11;
    const SERVICE__CONNECTED_WEBSITE = 12;
    const SERVICE__PASSPORT_DATA = 13;

    /**
     * @brief Constructor-initializer
     * @param array $data JSON-decoded chat data received from Telegram
     * @todo Support for other fields like audio, video, sticker etc.
     */
    public function __construct(array $data)
    {
        $this->id = $data['message_id'] ?? $data['id'] ?? 0;
        $this->date = new \DateTime('@' . $data['date']);
        $this->chat = new Chat($data['chat']);
        $this->from = isset($data['from']) ? new User($data['from']) : null;
        $this->forwardFrom = isset($data['forward_from']) ? new User($data['forward_from']) : null;
        $this->forwardDate = isset($data['forward_date']) ? new \DateTime('@' . $data['forward_date']) : null;
        $this->replyTo = isset($data['reply_to_message']) ? new Message($data['reply_to_message']) : null;
        $this->text = $data['text'] ?? "";
        if (isset($data['entities'])) {
            $this->entities = [];
            foreach ($data['entities'] as $entity)
                $this->entities[] = new MessageEntity($entity);
        }
    }

    /**
     * @brief Returns message ID
     * @return int Message unique identifier
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @brief Returns message instigator (user who sent this message)
     * @return User Message instigator
     */
    public function getInstigator(): User
    {
        return $this->chat;
    }

    /**
     * @brief Returns class name
     * @return string "\VTg\Objects\Message"
     */
    public function getClass(): string
    {
        return "\VTg\Objects\Message";
    }

    /**
     * @brief Returns Telegram type name
     * @return string "message"
     */
    public function getType(): string
    {
        return "message";
    }

    /**
     * @brief Creates an action to answer this message
     * @param string $text Message text
     * @param array $extraParameters Extra parameters for request if needed
     * @return Action "Send message" action (ready to execute)
     */
    public function answer(string $text, array $extraParameters = []): Action
    {
        return Action::sendMessage($this->chat->id, $text, $extraParameters);
    }

    /**
     * @brief Creates an action to answer this message with reply
     * @param string $text Message text
     * @param array $extraParameters Extra parameters for request if needed
     * @return Action "Send message" action (ready to execute)
     */
    public function reply(string $text, array $extraParameters = []): Action
    {
        $extraParameters['reply_to_message_id'] = $this->id;
        return Action::sendMessage($this->chat->id, $text, $extraParameters);
    }

    /**
     * @brief Creates an action to reply this message
     * @param string $text Message text
     * @param int|string $chatId Unique identifier for the target chat or @username of the target channel
     * @param bool $disableNotification If true, message will be forwarded silently (without a notification for users)
     * @return Action "Forward message" action (ready to execute)
     */
    public function forward(string $chatId, bool $disableNotification = false): Action
    {
        return Action::callAPIMethod('forwardMessage', $chatId, $this->chat->id, $this->id, $disableNotification);
    }
}
