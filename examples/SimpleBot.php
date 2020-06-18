<?php

require_once __DIR__ . '/../src/autoload.php';

class SimpleBot extends VTgBot
{
}

SimpleBot::setToken("<token>");
SimpleBot::registerCommandHandler('help', function (VTgBotController $bot, VTgMessage $message, string $data) {
    $answer = '/help with data: ' . $data;
    $bot->execute(VTgAction::sendMessage($message->chat->id, $answer));
});
SimpleBot::registerStandardMessageHandler(function (VTgBotController $bot, VTgMessage $message) {
    $greeting = 'Hello! ' . $message->chat->firstName;
    $bot->execute(VTgAction::sendMessage($message->chat->id, $greeting));
});

$result = SimpleBot::processUpdatePost();
