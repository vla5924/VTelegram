<?php

require_once __DIR__ . '/VTgObject.php';
require_once __DIR__ . '/VTgUser.php';

/**
 * @brief Class to represent an inline query
 * @todo Location support
 */
class VTgInlineQuery extends VTgObject
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
}
