<?php

require_once VTELEGRAM_REQUIRE_DIR . '/VTelegram.php';

class VTgBot
{
    static private $token = "";
    static private $tg = new VTelegram();

    static public function setToken(string $token): void
    {
        self::$tg->updateToken($token);
    }

    abstract static public function processMessage();

    static public function processCommand(string $command, array $data = [])
    {

    }
}