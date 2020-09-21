<?php

namespace VTg\Tools;

/**
 * @brief Generator of reply keyboards
 * @details Class provides a set of functions for convenient generating reply_markup content for messages sent via Telegram Bot API
 * @todo Documentation
 */
class ReplyKeyboard
{
    const ONE_TIME = 1; ///< Requests clients to resize the keyboard vertically for optimal fit
    const RESIZE = 2; ///< Requests clients to hide the keyboard as soon as it's been used
    const SELECTIVE = 4; ///< Use this parameter if you want to show the keyboard to specific users only

    /**
     * @var array $keyboard
     * @brief Array of array of buttons
     */
    protected $keyboard = [];

    /**
     * @brief Constructor-initializer
     * @param array $keyboard Array of array of buttons
     */
    public function __construct(array $keyboard)
    {
        $this->keyboard = $keyboard;
    }

    /**
     * @brief Converts keyboard array into a valid for reply_markup parameter (in API methods) object
     * @param int $options Bitmask of parameters (see ONE_TIME, RESIZE, SELECTIVE)
     * @return array Reply markup as associative array ready to be passed in API methods
     */
    public function assoc(int $options = 0): array
    {
        return ['reply_keyboard' => [
            'keyboard' => $this->keyboard,
            'resize_keyboard' => (bool) ($options & self::RESIZE),
            'one_time_keyboard' => (bool) ($options & self::ONE_TIME),
            'selective' => (bool) ($options & self::SELECTIVE)
        ]];
    }

    /**
     * @brief Converts keyboard array into a valid for reply_markup parameter (in API methods) string
     * @param int $options Bitmask of parameters (see ONE_TIME, RESIZE, SELECTIVE)
     * @return string JSON-serialized reply markup ready to be passed in API methods
     */
    public function json(int $options = 0): string
    {
        return json_encode($this->assoc($options));
    }

    /**
     * @brief Simple button generator
     * @details Generates array describing a simple button from data passed in various formats.
     * @param array|string $data Button data or text
     * @return array Valid array of button parameters
     * @todo request_contact, request_location, request_poll parameters
     */
    static public function button($data): array
    {
        if (gettype($data) === "string")
            $text = $data;
        else
            $text = $data[0] ?? $data['text'];
        $button = ['text' => $text];
        return $button;
    }

    /**
     * @brief Generates a keyboard with one button
     * @param array $button Array describing a button
     * @return ReplyKeyboard Reply keyboard object with prepared keyboard
     */
    static public function single(array $button): self
    {
        $keyboard = [[self::button($button)]];
        return new self($keyboard);
    }

    /**
     * @brief Generates a keyboard with one button (wrapper for single())
     * @param string $text Text displayed on a button
     * @param string $callbackData Callback data (used as a payload in API methods)
     * @return ReplyKeyboard Reply keyboard object with prepared keyboard
     */
    static public function singleP(string $text, string $callbackData): self
    {
        return self::single([$text, $callbackData]);
    }

    /**
     * @brief Generates a keyboard with column of buttons
     * @param array $buttons1d Array of arrays describing buttons
     * @return ReplyKeyboard Reply keyboard object with prepared keyboard
     */
    static public function column(array $buttons1d): self
    {
        $keyboard = [];
        foreach ($buttons1d as $button)
            $keyboard[] = [self::button($button)];
        return new self($keyboard);
    }

    /**
     * @brief Generates a keyboard with column of buttons (wrapper for column())
     * @param array $buttons Arrays describing buttons
     * @return ReplyKeyboard Reply keyboard object with prepared keyboard
     */
    static public function columnP(array ...$buttons): self
    {
        return self::column($buttons);
    }

    /**
     * @brief Generates a keyboard with row of buttons
     * @param array $buttons1d Array of arrays describing buttons
     * @return ReplyKeyboard Reply keyboard object with prepared keyboard
     */
    static public function row(array $buttons1d): self
    {
        $keyboard = [[]];
        foreach ($buttons1d as $button)
            $keyboard[0][] = self::button($button);
        return new self($keyboard);
    }

    /**
     * @brief Generates a keyboard with row of buttons (wrapper for row())
     * @param array $buttons Arrays describing buttons
     * @return ReplyKeyboard Reply keyboard object with prepared keyboard
     */
    static public function rowP(array ...$buttons): self
    {
        return self::row($buttons);
    }

    /**
     * @brief Generates a keyboard with buttons grid
     * @details Number of rows depends on number of buttons, each row contains not more than given number of buttons
     * @param int $columns Number of columns in each row
     * @param array $buttons1d Array of arrays describing buttons
     * @return ReplyKeyboard Reply keyboard object with prepared keyboard
     */
    static public function grid(int $columns, array $buttons1d): self
    {
        $keyboard = [];
        $i = 0;
        foreach ($buttons1d as $button)
            $keyboard[floor($i++ / $columns)][] = self::button($button);
        return new self($keyboard);
    }

    /**
     * @brief Generates a keyboard with buttons grid (wrapper for grid())
     * @details See grid().
     * @param int $columns Number of columns in each row
     * @param array $buttons Array of arrays describing buttons
     * @return ReplyKeyboard Reply keyboard object with prepared keyboard
     */
    static public function gridP(int $columns, array ...$buttons): self
    {
        return self::grid($columns, $buttons);
    }

    /**
     * @brief Generates a keyboard with given positioned buttons
     * @details Keyboard is generated "as is", buttons are placed on rows and columns according to their order in the array.
     * @param array $buttons2d Array of arrays of arrays describing buttons
     * @return ReplyKeyboard Reply keyboard object with prepared keyboard
     */
    static public function table(array $buttons2d): self
    {
        $keyboard = [];
        $rowNum = 0;
        foreach ($buttons2d as $row) {
            foreach ($row as $button) {
                $keyboard[$rowNum][] = self::button($button);
            }
            $rowNum++;
        }
        return new self($keyboard);
    }
}
