<?php

require_once __DIR__ . '/../VTgObjects/VTgInlineQueryResults/VTgIQRArticle.php';

class VTgIQRTemplate
{
    const PARAM_ID = 'id';
    const PARAM_TITLE = 'title';
    const PARAM_DESCRIPTION = 'description';
    const PARAM_URL = 'url';
    const PARAMETERS = [
        '%i' => self::PARAM_ID,
        '%t' => self::PARAM_TITLE,
        '%d' => self::PARAM_DESCRIPTION,
        '%u' => self::PARAM_URL
    ];

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
