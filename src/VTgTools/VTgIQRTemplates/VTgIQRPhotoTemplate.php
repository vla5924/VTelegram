<?php

require_once __DIR__ . '/VTgIQRTemplate.php';
require_once __DIR__ . '/../../VTgObjects/VTgInlineQueryResults/VTgIQRPhoto.php';

/**
 * @brief Generator for arrays of inline query photo results
 */
class VTgIQRPhotoTemplate extends VTgIQRTemplate
{
    const ID = 'id'; ///< Unique result identifier parameter name
    const PHOTO_URL = 'photo_url'; ///< A valid URL of the photo (JPEG, 5MB max)
    const THUMB_URL = 'thumb_url'; ///< URL of the thumbnail for the photo
    const WIDTH = 'width'; ///< Width of the photo
    const HEIGHT = 'height'; ///< Height of the photo
    const TITLE = 'title'; ///< Title of the result parameter name
    const DESCRIPTION = 'description'; ///< Description parameter name
    const CAPTION = 'caption'; ///< Description parameter name

    const PARAMETERS = [
        '%i' => self::ID,
        '%p' => self::PHOTO_URL,
        '%u' => self::THUMB_URL,
        '%w' => self::WIDTH,
        '%h' => self::HEIGHT,
        '%t' => self::TITLE,
        '%d' => self::DESCRIPTION,
        '%c' => self::CAPTION
    ]; ///< Parameters and their placeholders

    const PARAMETERS_REQUIRED = [self::ID, self::PHOTO_URL, self::THUMB_URL]; ///< Required parameters

    /**
     * @brief Generates an array with VTgIQRArticle objects - inline query photo results
     * @details There are several placeholders for message template available:
     * @code{.txt}
     * %i - 'id' field
     * %p - 'photo_url' field
     * %u - 'thumb_url' field
     * %w - 'width' field
     * %h - 'height' field
     * %t - 'title' field
     * %d - 'description' field
     * %c - 'caption' field
     * @endcode
     * @note Each element in $entries array must have the following fields: id, photo_url, thumb_url
     * @param array $entries Array with entries (articles data)
     * @param string|null $messageTemplate Message template if needed (with placeholders)
     * @param string|null $parseMode Parse mode for input message content
     * @param bool $disableWebPagePreview Disable web page preview for input message content
     * @return array Array of VTgIQRArticle objects
     */
    static public function make(array $entries, string $messageTemplate = null, string $parseMode = null, bool $disableWebPagePreview = false): array
    {
        $result = [];
        foreach ($entries as $entry) {
            $extraParameters = array_filter($entry, function ($key) {
                return array_search($key, self::PARAMETERS_REQUIRED) === false;
            }, ARRAY_FILTER_USE_KEY);
            if ($messageTemplate) {
                $text = self::format($messageTemplate, $entry);
                $inputMessageContent = new VTgIMCText($text, $parseMode, $disableWebPagePreview);
                $result[] = new VTgIQRPhoto($entry[self::ID], $entry[self::PHOTO_URL], $entry[self::THUMB_URL], $extraParameters, $inputMessageContent);
            } else {
                $result[] = new VTgIQRPhoto($entry[self::ID], $entry[self::PHOTO_URL], $entry[self::THUMB_URL], $extraParameters);
            }
        }
        return $result;
    }
}
