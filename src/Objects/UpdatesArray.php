<?php

namespace VTg\Objects;

use VTg\Objects\BaseObject;
use VTg\Objects\Update;

/**
 * @brief Class to represent array of updates returned by getUpdates API method
 */
class UpdatesArray extends BaseObject
{
    /**
     * @var array $array
     * @brief Array of VTgUpdate objects
     */
    public $array = [];

    /**
     * @brief Constructor-initializer
     * @param array $data JSON-decoded array of updates data received from Telegram
     */
    public function __construct(array $data) {
        foreach ($data as $update)
            $this->array[] = new Update($update);
    }
}
