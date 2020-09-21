<?php

namespace VTg\Objects\IQR;

use VTg\Objects\IQR\BaseIQR;
use VTg\Objects\IMC\BaseIMC;

/**
 * @brief Class for Inline Query Result Article type
 * @details Represents a link to an article or web page.
 */
class Article extends BaseIQR
{
    const TYPE = 'article'; ///< Type of the result

    /**
     * @var string $title
     * @brief Title of the result
     */
    public $title;

    /**
     * @var BaseIMC $inputMessageContent
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
     * @param BaseIMC $inputMessageContent Content of the message to be sent
     * @param array $extraParameters Other parameters if needed
     */
    public function __construct(string $id, string $title, BaseIMC $inputMessageContent, array $extraParameters = [])
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