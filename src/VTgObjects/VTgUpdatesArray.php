<?php

require_once __DIR__ . '/VTgObject.php';
require_once __DIR__ . '/VTgUpdate.php';

class VTgUpdatesArray extends VTgObject
{
    public $array = [];

    public function __construct(array $data) {
        foreach ($data as $update)
            $this->array[] = new VTgUpdate($update);
    }
}
