<?php

namespace VTg\MetaObjects;

use VTg\Objects\User;

/**
 * @brief Service class for storing information about Telegram user with data from SQL database
 * @details Legacy class for removed DBAuth bot module
 */
class AuthUser
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
     * @var User|null $u
     * @brief Telegram user object
     */
    public $u = null;

    /**
     * @brief Constructor-initializer
     * @param bool $isNew True if user writes to bot for a first time
     * @param array $fields Info about user stored in database
     * @param User|null $user Telegram user object
     */
    public function __construct(bool $isNew, array $fields, User $user = null)
    {
        $this->isNew = $isNew;
        $this->fields = $fields;
        $this->u = $user;
    }
}
