<?php

namespace VTg\Objects;

use VTg\Objects\User;

/**
 * @brief Interface to be implemented by handlable objects (object that are passed to VTgBot handlers)
 */
interface Handlable
{
    /**
     * @brief Returns object ID (unique among objects of same type)
     * @return int|string Object unique identifier
     */
    public function getId();

    /**
     * @brief Returns object instigator (user who initiated this object creation by sending message or callback query etc.)
     * @return User Instigator
     */
    public function getInstigator(): User;

    /**
     * @brief Returns class name
     * @return string Class name
     */
    public function getClass(): string;

    /**
     * @brief Returns Telegram type name
     * @return string Telegram type name
     */
    public function getType(): string;
}
