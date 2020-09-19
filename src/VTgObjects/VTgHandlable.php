<?php

require_once __DIR__ . '/VTgUser.php';

interface VTgHandlable
{
    public function getId();

    public function getInstigator(): VTgUser;
}
