<?php

require_once __DIR__ . '/../VTgObject.php';

/**
 * @brief Base class for IMC - Input Message Content in inline query results
 */
abstract class VTgIMC extends VTgObject
{
    /**
     * @brief Converts object data to array
     * @return array Array
     */
    abstract public function toArray(): array;
}
