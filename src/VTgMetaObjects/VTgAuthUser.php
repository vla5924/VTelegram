<?php

require_once __DIR__ . '/../VTgObjects/VTgUser.php';

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
     * @var VTgUser|null $u
     * @brief Telegram user object
     */
    public $u = null;

    /**
     * @brief Constructor-initializer
     * @param bool $isNew True if user writes to bot for a first time
     * @param array $fields Info about user stored in database
     * @param VTgUser|null $user Telegram user object
     */
    public function __construct(bool $isNew, array $fields, VTgUser $user = null)
    {
        $this->isNew = $isNew;
        $this->fields = $fields;
        $this->u = $user;
    }
}
