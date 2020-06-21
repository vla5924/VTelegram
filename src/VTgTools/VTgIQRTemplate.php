<?php

require_once __DIR__ . '/../VTgObjects/VTgInlineQueryResults/VTgIQRArticle.php';

/**
 * @brief Generator for arrays of inline query results
 */
class VTgIQRTemplate
{
    const PARAM_ID = 'id'; ///< Unique result identifier parameter name
    const PARAM_TITLE = 'title'; ///< Title of the result parameter name
    const PARAM_DESCRIPTION = 'description'; ///< Description parameter name
    const PARAM_URL = 'url'; ///< URL parameter name
    const PARAMETERS = [
        '%i' => self::PARAM_ID,
        '%t' => self::PARAM_TITLE,
        '%d' => self::PARAM_DESCRIPTION,
        '%u' => self::PARAM_URL
    ]; ///< Array with parameters and their placeholders

    /**
     * @brief Replaces placeholders with given entry parameters
     * @details There are several placeholders available:
     * @code{.txt}
     * %i - 'id' field,
     * %t - 'title' field,
     * %d - 'description' field,
     * %u - 'url' field
     * @endcode
     * @param string $messageTemplate Message template (with placeholders if needed)
     * @param array $entry Array with entry data
     * @return string Formatted string
     */
    static public function format(string $messageTemplate, array $entry = []): string
    {
        $search = [];
        $replace = [];
        foreach (self::PARAMETERS as $placeholder => $key) {
            if (isset($entry[$key])) {
                $search[] = $placeholder;
                $replace[] = $entry[$key];
            }
        }
        return str_replace($search, $replace, $messageTemplate);
    }

    /**
     * @brief Generates an array with VTgIQRArticle objects - inline query result articles
     * @param array $entries Array with entries (articles data)
     * @param string $messageTemplate Message template (with placeholders if needed)
     * @param string|null $parseMode Parse mode for input message content
     * @return array Array of VTgIQRArticle objects
     */
    static public function articles(array $entries, string $messageTemplate, string $parseMode = null): array
    {
        $result = [];
        foreach ($entries as $entry) {
            $text = self::format($messageTemplate, $entry);
            $inputMessageContent = new VTgIMCText($text);
            if ($parseMode)
                $inputMessageContent->parseMode = $parseMode;
            $extraParameters = array_filter($entry, function ($key) {
                return array_search($key, self::PARAMETERS) === false;
            }, ARRAY_FILTER_USE_KEY);
            $result[] = new VTgIQRArticle($entry[self::PARAM_ID], $entry[self::PARAM_TITLE], $inputMessageContent, $extraParameters);
        }
        return $result;
    }
}
