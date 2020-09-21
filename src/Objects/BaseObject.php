<?php

namespace VTg\Objects;

/**
 * @brief Base class for Telegram objects (messages, media, chats etc.)
 * @details Basically, this class added for polymorph abstractions in other VTg-classes
 */
class BaseObject
{
    /**
     * @brief Default constructor
     * @details Does nothing
     * @param array $data Array with some data
     */
    public function __construct(array $data = [])
    {
    }
}
