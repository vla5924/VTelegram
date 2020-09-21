<?php

namespace VTg\Objects\IMC;

use VTg\Objects\BaseObject;

/**
 * @brief Base class for IMC - Input Message Content in inline query results
 */
abstract class BaseIMC extends BaseObject
{
    /**
     * @brief Converts object data to array
     * @return array Array
     */
    abstract public function toArray(): array;
}
