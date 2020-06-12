<?php

require_once __DIR__ . '/VTgUser.php';

/**
 * @brief Class to represent information about chat
 */
class VTgChat extends VTgUser
{
    /**
     * @var int $type
     * @brief Chat type code
     */
    public $type = 0;

    /**
     * @var string $title
     * @brief Chat title (if provided)
     */
    public $title = "";

    /**
     * @var bool $allMembersAreAdmininstrators
     * @brief Flag if all chat members are administrators (if needed)
     */
    public $allMembersAreAdmininstrators = true;

    const TYPE__PRIVATE = 0;    ///< Chat is private
    const TYPE__GROUP = 1;      ///< Chat is a group
    const TYPE__SUPERGROUP = 2; ///< Chat is a supergroup
    const TYPE__CHANNEL = 3;    ///< Chat is a channel

    /**
     * @brief Service function for type detecting
     * @param string $stringType String contains chat type name
     * @return int Chat type code (see $code)
     */
    protected function detectType(string $stringType): int
    {
        switch($stringType) {
            case 'group':
                return 1;
            case 'supergroup':
                return 2;
            case 'channel':
                return 3;
            default:
                return 0;
        }
    }

    /**
     * @brief Constructor-initializer
     * @param array $data JSON-decoded chat data received from Telegram
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->type = $this->detectType($data['type']);
        $this->title = $data['title'] ?? "";
        $this->allMembersAreAdmininstrators = $data['all_members_are_administrators'] ?? true;
    }
}