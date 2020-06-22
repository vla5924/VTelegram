<?php

require_once __DIR__ . '/../VTgBot.php';
require_once __DIR__ . '/../VTgMetaObjects/VTgAuthUser.php';
require_once __DIR__ . '/../VTgHandlers/VTgHandler.php';

/**
 * @class VTgDBAuth
 * @extends VTgBot
 * @brief Trait for user authentication mechanism with MySQL (or literally any other SQL) database
 * @warning This is a trait, not a class (unfortunately, Doxygen does not
 * support PHP traits so it looks like a class in documentation)
 * @todo Add database vendor class (maybe SafeMySQL or whatever)
 */
trait VTgDBAuth
{
    /**
     * @var $db
     * @brief Database "connection" instance (an object of class implementing database engine API)
     */
    static protected $db;

    /**
     * @var string $usersTableName
     * @brief Name of SQL table with info about users
     */
    static protected $usersTableName = "users";

    /**
     * @var string $tgIdFieldName
     * @brief Name of field (SQL column) for Telegram users identifiers
     */
    static protected $tgIdFieldName = "id";

    /**
     * @var array $fields
     * @brief Data structure representing a user
     * @details 1-dimensional array with keys as field names (without ``) and
     * values as defaults. For example:
     * @code
     * [
     *   'id'            => 0,
     *   'name'          => '',
     *   'position'      => 'main_menu',
     *   'language'      => 'ru',
     *   'last_activity' => 0,
     * ];
     * @endcode
     * @note Fields in SQL table must have same names (basically, this array
     * describes a structure of table which holds user data, e.g. name, some 
     * points, last activity timestamp...)
     * @warning You need to provide the name of "Telegram ID field" (same with
     * $tgIdFieldName) even it is provided in this special property!
     */
    static protected $fields = [];

    /**
     * @memberof VTgDBAuth
     * @brief Saves some database parameters
     * @param mixed $db Database "connection" instance
     * @param array $fields Array of fields (data structure representing a user)
     */
    static public function setUpDatabase($db, string $usersTableName = "users", string $tgIdFieldName = "id", array $fields = []): void
    {
        self::$db = $db;
        self::$usersTableName = $usersTableName;
        self::$tgIdFieldName = $tgIdFieldName;
        self::$fields = $fields;
    }

    /**
     * @memberof VTgDBAuth
     * @brief Serializes an array in SQL-style: `key` = 'value`, ...
     * @param array $fields Serializable array of fields with values
     * $return string Serialized fields
     */
    static protected function getFieldsString(array $fields = []): string
    {
        $implodedFields = [];
        foreach ($fields as $name => $value)
            $implodedFields[] = sprintf("`%s` = '%s'", $name, $value);
        return implode(', ', $implodedFields);
    }

    /**
     * @memberof VTgDBAuth
     * @brief Makes SQL query string for inserting a row with info about user
     * @param int $tgId Telegram user identifier
     * @param array $fields Fields with non-default values if needed
     * @return string SQL query string
     */
    static protected function getInsertQueryString(int $tgId, array $fields = []): string
    {
        $readyFields = array_merge(self::$fields, $fields);
        $readyFields[self::$tgIdFieldName] = $tgId;
        $fieldsString = self::getFieldsString($readyFields);
        return sprintf("INSERT INTO `%s` SET %s", self::$usersTableName, $fieldsString);
    }

    /**
     * @memberof VTgDBAuth
     * @brief Makes SQL query string for selection a row with info about user
     * @param int $tgId Telegram user identifier
     * @return string SQL query string
     */
    static protected function getSelectQueryString(int $tgId): string
    {
        return sprintf("SELECT * FROM `%s` WHERE `%s` = %d", self::$usersTableName, self::$tgIdFieldName, $tgId);
    }

    /**
     * @memberof VTgDBAuth
     * @brief Makes query to database for selecting a row
     * @note Implement it according to database "connection" class API so it
     * could return 1D-array with field names and keys and its values
     * @param string $query SQL query string
     * @return array|null Query result
     */
    abstract static protected function makeSelectRowQuery(string $query): ?array;

    /**
     * @memberof VTgDBAuth
     * @brief Makes query to database for inserting a row
     * @note Implement it according to database "connection" class API so it
     * could return true if insertion was succeed
     * @param string $query SQL query string
     * @return bool Query result
     */
    abstract static protected function makeInsertRowQuery(string $query): bool;

    /**
     * @memberof VTgDBAuth
     * @brief Checks and authorizes Telegram user according to info in database
     * @details This method will try to select a row with info about a user with
     * given Telegram ID. If found, it will return meta-object with all the data
     * otherwise it will insert a row to SQL table and return meta-object with
     * default fields values.
     * @param VTgUser $user Telegram user to authorize
     * @return VTgAuthUser Meta-object with info about user
     */
    static public function authorizeUser(VTgUser $user): VTgAuthUser
    {
        $tgId = $user->id;
        $fields = self::$fields;
        $fields[self::$tgIdFieldName] = $tgId;
        $dbFields = self::makeSelectRowQuery(self::getSelectQueryString($tgId));
        if (!$dbFields or empty($dbFields)) {
            self::makeInsertRowQuery(self::getInsertQueryString($tgId));
            return new VTgAuthUser(true, $fields, $user);
        }
        $fields = array_merge($fields, $dbFields);
        return new VTgAuthUser(false, $fields, $user);
    }

    static public function getPreHandlerAutoAuthName(): string
    {
        return 'DBAuth_autoAuth';
    }

    static public function enableAutoAuthorization(): void
    {
        VTgHandler::addPreHandler(self::getPreHandlerAutoAuthName(), function (int $handlerType, ...$args) {
            switch ($handlerType):
                case VTgHandler::CALLBACK_QUERY:
                case VTgHandler::CHOSEN_INLINE_RESULT:
                case VTgHandler::DYNAMIC_CALLBACK_QUERY:
                case VTgHandler::INLINE_QUERY:
                    $user = $args[1]->from;
                    break;
                case VTgHandler::COMMAND_FALLBACK:
                case VTgHandler::COMMAND:
                case VTgHandler::DYNAMIC_COMMAND:
                case VTgHandler::SIMPLE_COMMAND:
                case VTgHandler::STANDARD_MESSAGE:
                    $user = $args[1]->from;
                    break;
            endswitch;
            return static::authorizeUser($user);
        });
    }
}
