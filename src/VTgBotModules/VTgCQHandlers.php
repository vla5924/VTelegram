<?php

require_once __DIR__ . '/../VTgBot.php';
require_once __DIR__ . '/VTgPatternChecker.php';

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
     * @details Each handler (being a callable type) must have a special header format, see registerStaticCQHandler()
     */
    static protected $callbackQueries = [];

    /**
     * @var array $dynamicCallbackQueries
     * @brief Array with dynamic callback query handlers
     * @details Each handler (being a callable type) must have a special header format, see registerDynamicCQHandler()
     */
    static protected $dynamicCallbackQueries = [];

    static protected $dynamicCallbackQueriesEnabled = true;

    use VTgPatternChecker;

    static public function enableDynamic(): void
    {
        static::$dynamicCallbackQueriesEnabled = true;
    }

    static public function disableDynamic(): void
    {
        static::$dynamicCallbackQueriesEnabled = false;
    }

    /**
     * @memberof VTgCQHandlers
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
    static public function registerStaticCQHandler(string $query, callable $handler): void
    {
        static::$callbackQueries[$query] = $handler;
    }

    /**
     * @memberof VTgCQHandlers
     * @brief Registers a function as a dynamic callback query handler
     * @details A handler will be passed VTgBotController object, VTgCallbackQuery object,
     * and array with query string itself and found %parameters 
     * So you can use it like this:
     * @code
     * VTgBot::registerDynamicCallbackQueryHandler('get_%d', function (VTgBotController $bot, VTgCallbackQuery $callbackQuery, array $parameters) {
     *   $newText = 'Get ID: ' . $parameters[1];
     *   $bot->execute(VTgAction::editMessageText($callbackQuery->message->chat->id, $callbackQuery->message->id, $newText));
     * });
     * @endcode
     * As you can see, you can provide command parameters as typed placeholder (just like in printf function):
     * percent sign and a letter. There are three placeholders available: 
     * %d - integer (set of 0-9 symbols, e.g. 123), 
     * %s - letters string (set of a-z, A-Z symbols, e.g. hello), 
     * %a - letters and numbers string (set of 0-9, a-z, A-Z symbols, e.g. h1ello23)
     * @param callable $handler Callback query handler [function (VTgBotController, VTgCallbackQuery, array): VTgAction]
     */
    static public function registerDynamicCQHandler(string $queryPattern, callable $handler): void
    {
        static::$dynamicCallbackQueries[$queryPattern] = $handler;
    }

    /**
     * @memberof VTgCQHandlers
     * @brief Hadles with a callback query
     * @param VTgCallbackQuery $callbackQuery Callback query data received from Telegram
     */
    static protected function handleCallbackQuery(VTgCallbackQuery $callbackQuery): void
    {
        $query = $callbackQuery->data;
        if (static::$dynamicCallbackQueriesEnabled and !empty(static::$dynamicCallbackQueries)) {
            foreach (static::$dynamicCallbackQueries as $pattern => $handler) {
                $parameters = [];
                if (self::checkMatch($pattern, $query, $parameters)) {
                    ($handler)(static::makeController(), $callbackQuery, $parameters);
                    return;
                }
            }
        }
        if (isset(static::$callbackQueries[$query])) {
            (static::$callbackQueries[$query])(static::makeController(), $callbackQuery);
            return;
        }
        if (static::$callbackQueryHandler) {
            (static::$callbackQueryHandler)(static::makeController(), $callbackQuery);
        }
    }
}
