<?php

/**
 * @brief Generator of reply keyboards
 * @details Class provides a set of functions for convenient generating reply_markup content for messages sent via Telegram Bot API
 * @todo Documentation
 */
class VTgReplyKeyboard
{
    const ONE_TIME = 1;
    const RESIZE = 2;
    const SELECTIVE = 4;

    protected $keyboard = [];

    public function __construct(array $keyboard)
    {
        $this->keyboard = $keyboard;
    }

    /**
     * @brief Converts keyboard array into a valid for reply_markup parameter (in API methods) string
     * @param int $options Bitmask of parameters (see ONE_TIME, RESIZE, SELECTIVE)
     * @return string Reply markup ready to be passed in API methods
     */
    public function make(int $options = 0): string
    {
        return json_encode(['reply_keyboard' => [
            'keyboard' => $this->keyboard,
            'resize_keyboard' => (bool) ($options & self::RESIZE),
            'one_time_keyboard' => (bool) ($options & self::ONE_TIME),
            'selective' => (bool) ($options & self::SELECTIVE)
        ]]);
    }

    /**
     * @brief Simple button generator
     * @details Generates array describing a simple button from data passed in various formats.
     * @param array $data Button data
     * @return array Valid array of button parameters
     */
    static public function button(array $data): array
    {
        $button = ['text' => $data[0] ?? $data['text']];
        if (isset($data[1]))
            $button['callback_data'] = $data[1];
        elseif (isset($data['callback_data']))
            $button['callback_data'] = $data['callback_data'];
        if (isset($data['url']))
            $button['url'] = $data['url'];
        return $button;
    }

    /**
     * @brief Generates a keyboard with one button
     * @param array $button Array describing a button
     * @return VTgReplyKeyboard Reply keyboard object with prepared keyboard
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
     * @return VTgReplyKeyboard Reply keyboard object with prepared keyboard
     */
    static public function singleP(string $text, string $callbackData): self
    {
        return self::single([$text, $callbackData]);
    }

    /**
     * @brief Generates a keyboard with column of buttons
     * @param array $buttons1d Array of arrays describing buttons
     * @return VTgReplyKeyboard Reply keyboard object with prepared keyboard
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
     * @return VTgReplyKeyboard Reply keyboard object with prepared keyboard
     */
    static public function columnP(array ...$buttons): self
    {
        return self::column($buttons);
    }

    /**
     * @brief Generates a keyboard with row of buttons
     * @param array $buttons1d Array of arrays describing buttons
     * @return VTgReplyKeyboard Reply keyboard object with prepared keyboard
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
     * @return VTgReplyKeyboard Reply keyboard object with prepared keyboard
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
     * @return VTgReplyKeyboard Reply keyboard object with prepared keyboard
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
     * @return VTgReplyKeyboard Reply keyboard object with prepared keyboard
     */
    static public function gridP(int $columns, array ...$buttons): self
    {
        return self::grid($columns, $buttons);
    }

    /**
     * @brief Generates a keyboard with given positioned buttons
     * @details Keyboard is generated "as is", buttons are placed on rows and columns according to their order in the array.
     * @param array $buttons2d Array of arrays of arrays describing buttons
     * @return VTgReplyKeyboard Reply keyboard object with prepared keyboard
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
