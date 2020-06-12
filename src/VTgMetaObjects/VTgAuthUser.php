<?php

require_once __DIR__ . '/../VTgUser.php';

/**
 * @brief Service class for storing information about Telegram user with data from SQL database
 * @details See VTgDBAuth trait
 */
class VTgAuthUser
{
    /**
     * @var bool $isNew
     * @brief True if user writes to bot for a first time
     */
    public $isNew = false;

    /**
     * @var array $fields
     * @brief Info about user stored in database
     */
    public $fields = [];

    /**
     * @var VTgUser|null $object
     * @brief Telegram user object
     */
    public $object = null;

    /**
     * @brief Constructor-initializer
     * @param bool $isNew True if user writes to bot for a first time
     * @param array $fields Info about user stored in database
     * @param VTgUser|null $object Telegram user object
     */
    public function __construct(bool $isNew, array $fields, VTgUser $object = null) {
        $this->isNew = $isNew;
        $this->fields = $fields;
        $this->object = $object;
    }
}
