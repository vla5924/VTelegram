<?php

require_once __DIR__ . '/../VTgBot.php';

/**
 * @class VTgCQHandlers
 * @extends VTgBot
 * @brief Trait for multi handlers for CQ - callback queries - mechanism (like commands)
 * @details It can be useful if you want to use callback query handlers like command handlers.
 * @warning This is a trait, not a class (unfortunately, Doxygen does not
 * support PHP traits so it looks like a class in documentation)
 */
trait VTgCQHandlers
{
    /**
     * @var callable|null $callbackQueryHandler
     * @brief Function for handling callback queries if needed
     * @details Handler must have a special header format, see registerCallbackQueryHandler()
     */
    static protected $callbackQueryHandler = null;

    /**
     * @var array $callbackQueries
     * @brief Array with callback query handlers
     * @details Each handler (being a callable type) must have a special header format, see registerCallbackQueryHandler()
     */
    static protected $callbackQueries = [];

    /**
     * @brief Registers a function as a callback query handler
     * @details A handler will be passed VTgBotController object and VTgCallbackQuery object.
     * So you can use it like this:
     * @code
     * VTgBot::registerCallbackQueryHandler('help_button', function (VTgBotController $bot, VTgCallbackQuery $callbackQuery) {
     *   $newText = 'Callback data: ' . $callbackQuery->data;
     *   $bot->execute(VTgAction::editMessageText($callbackQuery->message->chat->id, $callbackQuery->message->id, $newText));
     * });
     * @endcode
     * @param callable $handler Callback query handler [function (VTgBotController, VTgCallbackQuery): VTgAction]
     */
    static public function registerCallbackQueryHandler(string $query, callable $handler): void
    {
        static::$callbackQueries[$query] = $handler;
    }

    /**
     * @brief Hadles with a callback query
     * @param VTgCallbackQuery $callbackQuery Callback query data received from Telegram
     */
    static protected function handleCallbackQuery(VTgCallbackQuery $callbackQuery): void
    {
        $query = $callbackQuery->data;
        if (isset(static::$callbackQueries[$query])) {
            (static::$callbackQueries[$query])(static::makeController(), $callbackQuery);
        } elseif (static::$callbackQueryHandler) {
            (static::$callbackQueryHandler)(static::makeController(), $callbackQuery);
        }
    }
}
