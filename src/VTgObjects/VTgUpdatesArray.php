<?php

require_once __DIR__ . '/VTgObject.php';
require_once __DIR__ . '/VTgUpdate.php';

/**
 * @brief Class to represent array of updates returned by getUpdates API method
 */
class VTgUpdatesArray extends VTgObject
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
            $this->array[] = new VTgUpdate($update);
    }
}
