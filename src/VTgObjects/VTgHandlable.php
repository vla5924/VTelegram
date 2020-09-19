<?php

require_once __DIR__ . '/VTgUser.php';

/**
 * @brief Interface to be implemented by handlable objects (object that are passed to VTgBot handlers)
 */
interface VTgHandlable
{
    /**
     * @brief Returns object ID (unique among objects of same type)
     * @return int|string Object unique identifier
     */
    public function getId();

    /**
     * @brief Returns object instigator (user who initiated this object creation by sending message or callback query etc.)
     * @return VTgUser Instigator
     */
    public function getInstigator(): VTgUser;

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
