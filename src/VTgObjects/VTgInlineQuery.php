<?php

require_once __DIR__ . '/VTgObject.php';
require_once __DIR__ . '/VTgHandlable.php';
require_once __DIR__ . '/VTgUser.php';

/**
 * @brief Class to represent an inline query
 * @todo Location support
 */
class VTgInlineQuery extends VTgObject implements VTgHandlable
{
    public $id;
    public $from;
    public $query;
    public $offset;

    public function __construct(array $data)
    {
        $this->id = $data["id"];
        $this->from = new VTgUser($data["from"]);
        $this->query = $data["query"];
        $this->offset = $data["offset"];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getInstigator(): VTgUser
    {
        return $this->from;
    }

    public function getClass(): string
    {
        return "VTgInlineQuery";
    }

    public function getType(): string
    {
        return "inline_query";
    }
}
