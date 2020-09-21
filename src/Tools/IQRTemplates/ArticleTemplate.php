<?php

namespace VTg\Tools\IQRTemplates;

use VTg\Tools\IQRTemplates\BaseTemplate;
use VTg\Objects\IMC\Text;
use VTg\Objects\IQR\Article;

/**
 * @brief Generator for arrays of inline query article results
 */
class ArticleTemplate extends BaseTemplate
{
    const ID = 'id'; ///< Unique result identifier parameter name
    const TITLE = 'title'; ///< Title of the result parameter name
    const DESCRIPTION = 'description'; ///< Description parameter name
    const URL = 'url'; ///< URL parameter name

    const PARAMETERS = [
        '%i' => self::ID,
        '%t' => self::TITLE,
        '%d' => self::DESCRIPTION,
        '%u' => self::URL
    ]; ///< Parameters and their placeholders

    const PARAMETERS_REQUIRED = [self::ID, self::TITLE]; ///< Required parameters

    /**
     * @brief Generates an array with VTgIQRArticle objects - inline query result articles
     * @details There are several placeholders for message template available:
     * @code{.txt}
     * %i - 'id' field
     * %t - 'title' field
     * %d - 'description' field
     * %u - 'url' field
     * @endcode
     * @note Each element in $entries array must have the following fields: id, title
     * @param array $entries Array with entries (articles data)
     * @param string $messageTemplate Message template (with placeholders if needed)
     * @param string|null $parseMode Parse mode for input message content
     * @param bool $disableWebPagePreview Disable web page preview for input message content
     * @return array Array of Article objects
     */
    static public function make(array $entries, string $messageTemplate = null, string $parseMode = null, bool $disableWebPagePreview = false): array
    {
        $result = [];
        foreach ($entries as $entry) {
            $text = self::format($messageTemplate, $entry);
            $inputMessageContent = new Text($text, $parseMode, $disableWebPagePreview);
            $extraParameters = array_filter($entry, function ($key) {
                return array_search($key, self::PARAMETERS_REQUIRED) === false;
            }, ARRAY_FILTER_USE_KEY);
            $result[] = new Article($entry[self::ID], $entry[self::TITLE], $inputMessageContent, $extraParameters);
        }
        return $result;
    }
}
