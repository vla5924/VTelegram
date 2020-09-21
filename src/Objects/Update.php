<?php

namespace VTg\Objects;

use VTg\Objects\BaseObject;
use VTg\Objects\Message;
use VTg\Objects\CallbackQuery;
use VTg\Objects\InlineQuery;
use VTg\Objects\ChosenInlineResult;

/**
 * @brief Class (union-like structure) to represent update object received from Telegram
 * @todo Check if there are any more types in documentation
 */
class Update extends BaseObject
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
     * @var Message|null $message
     * @brief Message (if update type is "message")
     */
    public $message = null;

    /**
     * @var InlineQuery|null $inlineQuery
     * @brief Inline query (if update type is "inline query")
     */
    public $inlineQuery = null;

    /**
     * @var ChosenInlineResult|null $chosenInlineResult
     * @brief Chosen inline result (if update type is "chosen inline result")
     */
    public $chosenInlineResult = null;

    /**
     * @var CallbackQuery|null $callbackQuery
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
            $this->message = new Message($data['message']);
            return;
        }
        if (isset($data['callback_query'])) {
            $this->type = self::TYPE__CALLBACK_QUERY;
            $this->callbackQuery = new CallbackQuery($data['callback_query']);
            return;
        }
        if (isset($data['inline_query'])) {
            $this->type = self::TYPE__INLINE_QUERY;
            $this->inlineQuery = new InlineQuery($data['inline_query']);
            return;
        }
        if (isset($data['chosen_inline_result'])) {
            $this->type = self::TYPE__CHOSEN_INLINE_RESULT;
            $this->chosenInlineResult = new ChosenInlineResult($data['chosen_inline_result']);
            return;
        }
    }
}
