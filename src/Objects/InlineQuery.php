<?php

namespace VTg\Objects;

use VTg\Objects\BaseObject;
use VTg\Objects\Handlable;
use VTg\Objects\User;

/**
 * @brief Class to represent an inline query
 * @todo Location support
 */
class InlineQuery extends BaseObject implements Handlable
{
    /**
     * @var string $id
     * @brief Unique query identifier
     */
    public $id;

    /**
     * @var User $from
     * @brief Query sender
     */
    public $from;

    /**
     * @var string $query
     * @brief Text of the query (up to 256 characters)
     */
    public $query;

    /**
     * @var int $offset
     * @brief Offset of the results to be returned, can be controlled by the bot
     */
    public $offset;

    /**
     * @brief Constructor-initializer
     * @param array $data JSON-decoded inline query data received from Telegram
     */
    public function __construct(array $data)
    {
        $this->id = $data["id"];
        $this->from = new User($data["from"]);
        $this->query = $data["query"];
        $this->offset = $data["offset"];
    }

    /**
     * @brief Returns inline query ID
     * @return string Query unique identifier
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @brief Returns query instigator (user who initiated this query)
     * @return User Query instigator
     */
    public function getInstigator(): User
    {
        return $this->from;
    }

    /**
     * @brief Returns class name
     * @return string "VTgInlineQuery"
     */
    public function getClass(): string
    {
        return "VTgInlineQuery";
    }

    /**
     * @brief Returns Telegram type name
     * @return string "inline_query"
     */
    public function getType(): string
    {
        return "inline_query";
    }
}
