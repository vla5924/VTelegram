<?php

class VTgError
{
    public int $code;
    public string $description;

    public function __construct(int $code, string $description = "")
    {
        $this->code = $code;
        $this->description = $description;
    }
}