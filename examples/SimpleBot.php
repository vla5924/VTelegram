<?php

require_once __DIR__ . '/../src/autoload.php';

class SimpleBot extends VTgBot
{
}

SimpleBot::setToken("<token>");
SimpleBot::registerCommandHandler('help', function (VTgMessage &$message, string $data) {
    $answer = '/help with data: ' . $data;
    return VTgAction::sendMessage($message->chat->id, $answer);
});
SimpleBot::registerStandardMessageHandler(function (VTgMessage &$message) {
    $greeting = 'Hello! ' . $message->chat->firstName;
    return VTgAction::sendMessage($message->chat->id, $greeting);
});

$result = SimpleBot::processUpdatePost();
