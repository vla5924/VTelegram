<?php

require_once __DIR__ . '/../src/autoload.php';

class DynamicCommandsBot extends VTgBot
{
    use VTgDynamicCommands;
}

DynamicCommandsBot::setToken("<token>");
DynamicCommandsBot::registerDynamicCommandHandler('number_%d', function (VTgBotController $bot, VTgMessage $message, array $parameters, string $data) {
    $answer = 'You metioned number [' . $parameters[1] . '] with data: ' . $data;
    $bot->execute($message->reply($answer));
});
DynamicCommandsBot::registerStandardMessageHandler(function (VTgBotController $bot, VTgMessage $message) {
    $bot->execute($message->reply("This message has no command."));
});

DynamicCommandsBot::processUpdatePost();
