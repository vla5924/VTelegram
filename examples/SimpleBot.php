<?php

require_once __DIR__ . '/../src/autoload.php';

class SimpleBot extends VTgBot
{

}

SimpleBot::setToken("123");
SimpleBot::registerCommandHandler('help', function (VTgMessage &$message, string $data) {
    return VTgAction::sendMessage($message->chat->id, 'Help message');
});
SimpleBot::registerStandardMessageHandler(function(VTgMessage &$message) {
    return VTgAction::sendMessage($message->chat->id, 'Hello, ' . $message->chat->firstName);
});

$json = json_decode("[]", true);
$result = SimpleBot::processUpdateData($json);
