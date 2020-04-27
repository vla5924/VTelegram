<?php

class VTgUser
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