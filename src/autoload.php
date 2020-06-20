<?php

if (!defined('VTELEGRAM_BOT_MODULES')) {
    define('VTELEGRAM_BOT_MODULES', [
        'PatternChecker',
        'DBAuth',
        'DynamicCommands',
        'CQHandlers'
    ]);
}

if (!defined('VTELEGRAM_TOOLS')) {
    define('VTELEGRAM_TOOLS', [
        'Icon',
        'InlineKeyboard',
        'ReplyKeyboard',
        'IQRTemplate'
    ]);
}

require_once __DIR__ . '/VTgBot.php';
foreach (VTELEGRAM_BOT_MODULES as $moduleName)
    require_once __DIR__ . '/VTgBotModules/VTg' . $moduleName . '.php';
foreach (VTELEGRAM_TOOLS as $toolName)
    require_once __DIR__ . '/VTgTools/VTg' . $toolName . '.php';
