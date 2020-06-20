<?php

require_once __DIR__ . '/VTgIQR.php';
require_once __DIR__ . '/../VTgInputMessageContents/VTgIMC.php';

/**
 * @brief Class for Inline Query Result Photo type
 */
class VTgIQRPhoto extends VTgIQR
{
    const TYPE = 'photo'; ///< Type of the result

    /**
     * @var int|string $id
     * @brief Unique identifier for this result, 1-64 Bytes
     */
    public $id;

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
     * @var VTgIMC|null $inputMessageContent
     * @brief Content of the message to be sent
     */
    public $inputMessageContent = null;

    /**
     * @brief Constructor-initializer
     * @param int|string $id Unique identifier for this result, 1-64 Bytes
     * @param string $url A valid URL of the photo
     * @param string $thumbUrl URL of the thumbnail for the photo
     * @param array $extraParameters Other parameters if needed
     * @param VTgIMC|null $inputMessageContent Content of the message to be sent instead of the photo (if needed)
     */
    public function __construct(string $id, string $url, string $thumbUrl, array $extraParameters = [], VTgIMC $inputMessageContent = null)
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
