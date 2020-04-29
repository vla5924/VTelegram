<?php

require_once VTELEGRAM_REQUIRE_DIR . '/VTgObjects/VTgObject.php';

class VTgUser extends VTgObject
{
    public $id;
    public $firstName;
    public $lastName = "";
    public $username = "";

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? $data[0] ?? 0;
        $this->firstName = $data['first_name'] ?? $data[1] ?? "";
        $this->last_name = $data['last_name'] ?? "";
        $this->username = $data['username'] ?? "";
    }
}