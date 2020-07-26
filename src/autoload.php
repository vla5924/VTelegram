<?php

if (!defined('VTELEGRAM_BOT_MODULES')) {
    define('VTELEGRAM_BOT_MODULES', [
        'PatternChecker',
        'SimpleCommands',
        'DynamicCommands',
        'CQHandlers'
    ]);
}

if (!defined('VTELEGRAM_TOOLS')) {
    define('VTELEGRAM_TOOLS', [
        'Icon',
        'InlineKeyboard',
        'ReplyKeyboard'
    ]);
}

if (!defined('VTELEGRAM_TOOLS_IQR_TEMPLATES')) {
    define('VTELEGRAM_TOOLS_IQR_TEMPLATES', [
        'Article',
        'Photo'
    ]);
}

require_once __DIR__ . '/VTgBot.php';
foreach (VTELEGRAM_BOT_MODULES as $moduleName)
    require_once __DIR__ . '/VTgBotModules/VTg' . $moduleName . '.php';
foreach (VTELEGRAM_TOOLS as $toolName)
    require_once __DIR__ . '/VTgTools/VTg' . $toolName . '.php';
foreach (VTELEGRAM_TOOLS_IQR_TEMPLATES as $tplclassName)
    require_once __DIR__ . '/VTgTools/VTgIQRTemplates/VTgIQR' . $tplclassName . 'Template.php';
