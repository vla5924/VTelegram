<?php

require_once __DIR__ . '/../VTgObject.php';

/**
 * @brief Base class for IQR - Inline Query Result objects
 */
abstract class VTgIQR extends VTgObject
{
    const IMC_PARAM = 'input_message_content'; /// < Input message content parameter

    /**
     * @brief Converts object data to array
     * @return array Array
     */
    abstract public function toArray(): array;
}
