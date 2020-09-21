<?php

namespace VTg\Objects;

use VTg\Objects\BaseObject;

class MessageEntity extends BaseObject
{
    public $type;
    public $offset;
    public $length;
    public $url = null;

    const TYPE__UNKNOWN = 0;
    const TYPE__MENTION = 1;
    const TYPE__HASHTAG = 2;
    const TYPE__BOT_COMMAND = 3;
    const TYPE__URL = 4;
    const TYPE__EMAIL = 5;
    const TYPE__BOLD = 6;
    const TYPE__ITALIC = 7;
    const TYPE__CODE = 8;
    const TYPE__PRE = 9;
    const TYPE__TEXT_LINK = 10;


    protected function detectType(string $stringType): int
    {
        $type = 0;
        switch($stringType) {
            case 'mention':
                $type = 1;
            break;
            case 'hashtag':
                $type = 2;
            break;
            case 'bot_command':
                $type = 3;
            break;
            case 'url':
                $type = 4;
            break;
            case 'email':
                $type = 5;
            break;
            case 'bold':
                $type = 6;
            break;
            case 'italic':
                $type = 7;
            break;
            case 'code':
                $type = 8;
            break;
            case 'pre':
                $type = 9;
            break;
            case 'text_link':
                $type = 10;
            break;
            default:
            $type = 0;
        }
        return $type;
    }

    public function __construct(array $data) {
        $this->type = $this->detectType($data['type']);
        $this->offset = $data['offset'];
        $this->length = $data['length'];
        $this->url = $data['url'] ?? null;
    }
}