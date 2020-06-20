<?php

require_once __DIR__ . '/../VTgObjects/VTgObject.php';
require_once __DIR__ . '/../VTgObjects/VTgCallbackQuery.php';
require_once __DIR__ . '/../VTgObjects/VTgChat.php';
require_once __DIR__ . '/../VTgObjects/VTgFile.php';
require_once __DIR__ . '/../VTgObjects/VTgInlineQuery.php';
require_once __DIR__ . '/../VTgObjects/VTgMessage.php';
require_once __DIR__ . '/../VTgObjects/VTgMessageEntity.php';
require_once __DIR__ . '/../VTgObjects/VTgUpdate.php';
require_once __DIR__ . '/../VTgObjects/VTgUser.php';

require_once __DIR__ . '/../VTgObjects/VTgInputMessageContents/VTgIMC.php';
require_once __DIR__ . '/../VTgObjects/VTgInputMessageContents/VTgIMCText.php';

require_once __DIR__ . '/../VTgObjects/VTgInlineQueryResults/VTgIQRArticle.php';

require_once __DIR__ . '/VTgError.php';

/**
 * @brief Class represents a result of request to Telegram Bot API
 */
class VTgResult
{
    /**
     * @var bool $ok
     * @brief Flag if request was successful
     * @details If $ok is true, use $object property, otherwise check $error data
     */
    public $ok;

    /**
     * @var VTgObject|null $object
     * @brief Object with something that API can return
     * @details See VTgObject children
     */
    public $object = null;

    /**
     * @var VTgError|null $error
     * @brief Error data if request was wrong
     * @details See https://core.telegram.org/bots/api#making-requests
     */
    public $error = null;

    /**
     * @brief Construstor-initializer
     * @param bool $ok Flag if request was successful
     * @param VTgObject|VTgError $result Result of API call
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
     * @brief Constructs VTgResult from JSON-decoded array received from Telegram
     * @param array $data Data received from Telegram as a result of making a request
     * @param string $typename Classname of expected resulting object (generally one of VTgObject children, e.g. VTgMessage)
     * @return VTgResult Result object
     */
    static public function fromData(array $data, string $typename = "VTgObject") : VTgResult
    {
        $ok = $data['ok'] ?? false;
        if ($ok)
            return new self($ok, isset($data['result']) && is_array($data['result']) ? new $typename($data['result']) : null);
        return new self($ok, new VTgError($data['error_code'], $data['description']));
    }
}
