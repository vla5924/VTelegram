<?php

namespace VTg\Objects;

use VTg\Objects\BaseObject;

/**
 * @brief Class to represent information about Telegram user
 */
class User extends BaseObject
{
    /**
     * @var int $id
     * @brief Unique identifier
     */
    public $id;

    /**
     * @var string $firstName
     * @brief First name
     */
    public $firstName;

    /**
     * @var string $lastName
     * @brief Last name (if provided)
     */
    public $lastName = "";

    /**
     * @var string $username
     * @brief Username (if provided)
     */
    public $username = "";

    /**
     * @brief Constructor-initializer
     * @param array $data JSON-decoded user data received from Telegram
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? $data[0] ?? 0;
        $this->firstName = $data['first_name'] ?? $data[1] ?? "";
        $this->last_name = $data['last_name'] ?? "";
        $this->username = $data['username'] ?? "";
    }
}