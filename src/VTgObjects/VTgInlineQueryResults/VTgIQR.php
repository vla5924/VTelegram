<?php

require_once __DIR__ . '/../VTgObject.php';

/**
 * @brief Base class for IQR - Inline Query Result objects
 */
abstract class VTgIQR extends VTgObject
{
    /**
     * @brief Converts object data to array
     * @return array Array
     */
    abstract public function toArray(): array;
}
