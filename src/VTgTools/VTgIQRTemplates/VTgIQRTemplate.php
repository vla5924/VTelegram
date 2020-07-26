<?php

/**
 * @brief Base for generator of arrays of inline query results
 */
abstract class VTgIQRTemplate
{
    const PARAMETERS = []; ///< Parameters with their placeholders (overrided in children classes)

    /**
     * @brief Replaces placeholders with given entry parameters
     * @param string $messageTemplate Message template (with placeholders if needed)
     * @param array $entry Array with entry data
     * @return string Formatted string
     */
    static public function format(string $messageTemplate, array $entry = []): string
    {
        $search = [];
        $replace = [];
        foreach (static::PARAMETERS as $placeholder => $key) {
            if (isset($entry[$key])) {
                $search[] = $placeholder;
                $replace[] = $entry[$key];
            }
        }
        return str_replace($search, $replace, $messageTemplate);
    }

    /**
     * @brief Generates an array with VTgIQRArticle objects - inline query results (see children classes)
     * @param array $entries Array with entries (articles data)
     * @param string|null $messageTemplate Message template (with placeholders if needed)
     * @param string|null $parseMode Parse mode for input message content
     * @param bool $disableWebPagePreview Disable web page preview for input message content
     * @return array Array of VTgIQR objects
     */
    abstract static public function make(array $entries, string $messageTemplate = null, string $parseMode = null, bool $disableWebPagePreview = false): array;
}
