<?php

require_once __DIR__ . '/VTgIQR.php';
require_once __DIR__ . '/../VTgInputMessageContents/VTgIMC.php';

/**
 * @brief Class for Inline Query Result Article type
 * @details Represents a link to an article or web page.
 */
class VTgIQRArticle extends VTgIQR
{
    const TYPE = 'article'; ///< Type of the result

    /**
     * @var int|string $id
     * @brief Unique identifier for this result, 1-64 Bytes
     */
    public $id;

    /**
     * @var string $title
     * @brief Title of the result
     */
    public $title;

    /**
     * @var VTgIMC $inputMessageContent
     * @brief Content of the message to be sent
     */
    public $inputMessageContent;

    /**
     * @var array $extraParameters
     * @brief Other parameters if needed
     */
    public $extraParameters = [];

    /**
     * @brief Constructor-initializer
     * @param int|string $id Unique identifier for this result, 1-64 Bytes
     * @param string $title Title of the result
     * @param VTgIMC $inputMessageContent Content of the message to be sent
     * @param array $extraParameters Other parameters if needed
     */
    public function __construct(string $id, string $title, VTgIMC $inputMessageContent, array $extraParameters = [])
    {
        $this->id = $id;
        $this->title = $title;
        $this->inputMessageContent = $inputMessageContent;
        $this->extraParameters = $extraParameters;
    }

    /**
     * @brief Converts object data to array
     * @return array Array
     */
    public function toArray(): array
    {
        return array_merge([
            'type' => self::TYPE,
            'id' => $this->id,
            'title' => $this->title,
            self::IMC_PARAM => $this->inputMessageContent->toArray(),
        ], $this->extraParameters);
    }
}