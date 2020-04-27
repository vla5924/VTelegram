<?php

require_once __DIR__ . '/../src/autoload.php';

class SimpleBot extends VTgBot
{

}

SimpleBot::setToken("123");
SimpleBot::registerCommand('help', function (VTgMessage &$message, string $data) {
    return new VTgAction(VTgAction::ACTION__SEND_MESSAGE, $message->chat->id, 'Help message');
});
SimpleBot::registerStandardMessageHandler(function(VTgMessage &$message) {
    return new VTgAction(VTgAction::ACTION__SEND_MESSAGE, $message->chat->id, 'Hello, ' . $message->chat->firstName);
});

$json = json_decode("[]", true);
SimpleBot::processUpdateData($json);