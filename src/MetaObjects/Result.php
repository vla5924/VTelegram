<?php

namespace VTg\MetaObjects;

use VTg\MetaObjects\Error;

/**
 * @brief Class represents a result of request to Telegram Bot API
 */
class Result
{
    /**
     * @var bool $ok
     * @brief Flag if request was successful
     * @details If $ok is true, use $object property, otherwise check $error data
     */
    public $ok;

    /**
     * @var BaseObject|null $object
     * @brief Object with something that API can return
     * @details See Objects\Object children
     */
    public $object = null;

    /**
     * @var Error|null $error
     * @brief Error data if request was wrong
     * @details See https://core.telegram.org/bots/api#making-requests
     */
    public $error = null;

    /**
     * @brief Construstor-initializer
     * @param bool $ok Flag if request was successful
     * @param BaseObject|Error $result Result of API call
     */
    public function __construct(bool $ok, $result = null)
    {
        $this->ok = $ok;
        if ($this->ok)
            $this->object = $result;
        else
            $this->error = $result;
    }

    /**
     * @brief Wrapper for (maybe) shorter check if request was successful. 
     * @details E. g.
     * @code
     * if ($result()) { ... }
     * @endcode
     * instead of
     * @code
     * if ($result->ok) { ... }
     * @endcode
     * You may like it.
     * @return bool True if request was successful
     */
    public function __invoke(): bool
    {
        return $this->ok;
    }

    /**
     * @brief Constructs Result from JSON-decoded array received from Telegram
     * @param array $data Data received from Telegram as a result of making a request
     * @param string $typename Classname of expected resulting object (generally one of BaseObject children, e.g. Message)
     * @return Result Result object
     */
    static public function fromData(array $data, string $typename = "\VTg\Objects\BaseObject") : Result
    {
        $ok = $data['ok'] ?? false;
        if ($ok)
            return new self($ok, isset($data['result']) && is_array($data['result']) ? new $typename($data['result']) : null);
        return new self($ok, new Error($data['error_code'], $data['description']));
    }
}
