<?php

namespace VTg\Objects\IQR;

use VTg\Objects\BaseObject;

/**
 * @brief Base class for IQR - Inline Query Result objects
 */
abstract class BaseIQR extends BaseObject
{
    /**
     * @var int|string $id
     * @brief Unique identifier for this result, 1-64 Bytes
     */
    public $id;
    
    const IMC_PARAM = 'input_message_content'; ///< Input message content parameter
    const NO_EXTRA_PARAMETERS = []; ///< "No extra parameters" array

    /**
     * @brief Converts object data to array
     * @return array Array
     */
    abstract public function toArray(): array;
}
