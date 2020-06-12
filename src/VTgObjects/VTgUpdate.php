<?php

require_once __DIR__ . '/VTgObject.php';
require_once __DIR__ . '/VTgMessage.php';
require_once __DIR__ . '/VTgCallbackQuery.php';

/**
 * @brief Class (union-like structure) to represent update object received from Telegram
 * @todo Check if there are any more types in documentation
 */
class VTgUpdate extends VTgObject
{
    /**
     * @var int $id
     * @brief Unique identifier of update
     */
    public $id;

    /**
     * @var int $type
     * @brief Update type code
     */
    public $type = 0;

    /**
     * @var VTgMessage|null $message
     * @brief Message (if update type is "message")
     */
    public $message = null;

    /**
     * @var VTgInlineQuery|null $inlineQuery
     * @brief Inline query (if update type is "inline query")
     */
    public $inlineQuery = null;

    /**
     * @var VTgChosenInlineResult|null
     * @brief Chosen inline result (if update type is "chosen inline result")
     */
    public $chosenInlineResult = null;

    /**
     * @var VTgCallbackQuery|null $callbackQuery
     * @brief Callback query (if update type is "callback query")
     */
    public $callbackQuery = null;

    const TYPE__UNKNOWN = 0;              ///< "Unknown" update
    const TYPE__MESSAGE = 1;              ///< "Message" update
    const TYPE__INLINE_QUERY = 2;         ///< "Inline query" update
    const TYPE__CHOSEN_INLINE_RESULT = 3; ///< "Chosen inline result" update
    const TYPE__CALLBACK_QUERY = 4;       ///< "Callback query" update

    /**
     * @brief Constructor-initializer
     * @param array $data JSON-decoded update data received from Telegram
     * @todo Type handlers
     */
    public function __construct(array $data)
    {
        $this->id = $data['update_id'];
        if (isset($data['message'])) {
            $this->type = self::TYPE__MESSAGE;
            $this->message = new VTgMessage($data['message']);
            return;
        }
        if (isset($data['callback_query'])) {
            $this->type = self::TYPE__CALLBACK_QUERY;
            $this->callbackQuery = new VTgCallbackQuery($data['callback_query']);
            return;
        }
    }
}
