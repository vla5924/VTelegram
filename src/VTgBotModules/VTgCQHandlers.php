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
    protected static $callbackQueryHandler = null;

    /**
     * @var array $callbackQueries
     * @brief Array with callback query handlers
     * @details Each handler (being a callable type) must have a special header format, see registerStaticCQHandler()
     */
    protected static $callbackQueries = [];

    /**
     * @var array $dynamicCallbackQueries
     * @brief Array with dynamic callback query handlers
     * @details Each handler (being a callable type) must have a special header format, see registerDynamicCQHandler()
     */
    protected static $dynamicCallbackQueries = [];

    /**
     * @var bool $dynamicCallbackQueriesEnabled
     * @brief True if handlers for dynamic callback queries must be used
     */
    protected static $dynamicCQHandlersEnabled = false;

    use VTgPatternChecker;

    /**
     * @brief Enables dynamic callback queries handling
     */
    public static function enableDynamicCQHandlers(): void
    {
        static::$dynamicCQHandlersEnabled = true;
    }

    /**
     * @brief Disables dynamic callback queries handling
     */
    public static function disableDynamicCQHandlers(): void
    {
        static::$dynamicCQHandlersEnabled = false;
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
     * @param string $query Query data
     * @param callable $handler Callback query handler [function (VTgBotController, VTgCallbackQuery)]
     */
    public static function registerStaticCQHandler(string $query, callable $handler): void
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
     * @code{.txt} 
     * %d - integer (set of 0-9 symbols, e.g. 123), 
     * %s - letters string (set of a-z, A-Z symbols, e.g. hello), 
     * %a - letters and numbers string (set of 0-9, a-z, A-Z symbols, e.g. h1ello23)
     * @endcode
     * @param string $queryPattern Query data pattern
     * @param callable $handler Dynamic callback query handler [function (VTgBotController, VTgCallbackQuery, array)]
     */
    public static function registerDynamicCQHandler(string $queryPattern, callable $handler): void
    {
        static::$dynamicCallbackQueries[$queryPattern] = $handler;
    }

    /**
     * @memberof VTgCQHandlers
     * @brief Hadles with a callback query
     * @param VTgCallbackQuery $callbackQuery Callback query data received from Telegram
     */
    protected static function handleCallbackQuery(VTgCallbackQuery $callbackQuery): void
    {
        $query = $callbackQuery->data;
        if (static::$dynamicCQHandlersEnabled and !empty(static::$dynamicCallbackQueries)) {
            foreach (static::$dynamicCallbackQueries as $pattern => $handler) {
                $parameters = [];
                if (self::checkMatch($pattern, $query, $parameters)) {
                    ($handler)(static::makeController($callbackQuery), $callbackQuery, $parameters);
                    return;
                }
            }
        }
        if (isset(static::$callbackQueries[$query])) {
            (static::$callbackQueries[$query])(static::makeController($callbackQuery), $callbackQuery);
            return;
        }
        if (static::$callbackQueryHandler) {
            (static::$callbackQueryHandler)(static::makeController($callbackQuery), $callbackQuery);
        }
    }
}
