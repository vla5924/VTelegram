<?php

/**
 * @class VTgPatternChecker
 * @extends VTgBot
 * @brief Trait for checking pattern with placeholders
 * @details ...
 * @warning This is a trait, not a class (unfortunately, Doxygen does not
 * support PHP traits so it looks like a class in documentation)
 */
trait VTgPatternChecker
{
    /**
     * @memberof VTgPatternChecker
     * @brief Checks if a command matches a given pattern
     * @details You can provide command parameters as typed placeholder (just like in printf function).
     * There are three placeholders available: 
     * @code{.txt} 
     * %d - integer (set of 0-9 symbols, e.g. 123), 
     * %s - letters string (set of a-z, A-Z symbols, e.g. hello), 
     * %a - letters and numbers string (set of 0-9, a-z, A-Z symbols, e.g. h1ello23)
     * @endcode
     * @param string $pattern Pattern string (will be converted into regular expresson)
     * @param string $control String to check
     * @param [out] array $matchParameters Array with control string itself and found %parameters
     * @return bool True if a constrol string matches a pattern
     */
    protected static function checkMatch(string $pattern, string $control, array &$matchParameters = null): bool
    {
        $regExp = '/^' . str_replace(['%d', '%s', '%a'], ['([0-9]+)', '([A-Za-z]+)', '([0-9a-zA-Z]+)'], $pattern) . '$/';
        return preg_match($regExp, $control, $matchParameters) === 1;
    }
}
