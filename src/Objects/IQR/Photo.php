<?php

namespace VTg\Objects\IQR;

use VTg\Objects\IQR\BaseIQR;
use VTg\Objects\IMC\BaseIMC;

/**
 * @brief Class for Inline Query Result Photo type
 * @details Represents a link to a photo. By default, this photo will be sent 
 * by the user with optional caption. Alternatively, you can use VTgIMC object
 * to send a message with the specified content instead of the photo.
 */
class Photo extends BaseIQR
{
    const TYPE = 'photo'; ///< Type of the result

    /**
     * @var string $url
     * @brief A valid URL of the photo
     * @details Photo must be in jpeg format. Photo size must not exceed 5 MB.
     */
    public $url;

    /**
     * @var string $thumbUrl
     * @brief URL of the thumbnail for the photo
     */
    public $thumbUrl;

    /**
     * @var array $extraParameters
     * @brief Other parameters if needed
     */
    public $extraParameters = [];

    /**
     * @var BaseIMC|null $inputMessageContent
     * @brief Content of the message to be sent
     */
    public $inputMessageContent = null;

    /**
     * @brief Constructor-initializer
     * @param int|string $id Unique identifier for this result, 1-64 Bytes
     * @param string $url A valid URL of the photo
     * @param string $thumbUrl URL of the thumbnail for the photo
     * @param array $extraParameters Other parameters if needed
     * @param BaseIMC|null $inputMessageContent Content of the message to be sent instead of the photo (if needed)
     */
    public function __construct(string $id, string $url, string $thumbUrl, array $extraParameters = [], BaseIMC $inputMessageContent = null)
    {
        $this->id = $id;
        $this->url = $url;
        $this->thumbUrl = $thumbUrl;
        $this->extraParameters = $extraParameters;
        $this->inputMessageContent = $inputMessageContent;
    }

    /**
     * @brief Converts object data to array
     * @return array Array
     */
    public function toArray(): array
    {
        $parameters = [
            'type' => self::TYPE,
            'id' => $this->id,
            'photo_url' => $this->url,
            'thumb_url' => $this->thumbUrl,
        ];
        if ($this->inputMessageContent)
            $parameters[self::IMC_PARAM] = $this->inputMessageContent->toArray();
        return array_merge($parameters, $this->extraParameters);
    }
}
