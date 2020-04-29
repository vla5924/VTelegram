<?php

require_once VTELEGRAM_REQUIRE_DIR . '/VTgObjects/VTgUser.php';

class VTgChat extends VTgUser
{
    public int $type = 0;
    public string $title = "";
    public bool $allMembersAreAdmininstrators = true;

    const TYPE__PRIVATE = 0;
    const TYPE__GROUP = 1;
    const TYPE__SUPERGROUP = 2;
    const TYPE__CHANNEL = 3;

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

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->type = $this->detectType($data['type']);
        $this->title = $data['title'] ?? "";
        $this->allMembersAreAdmininstrators = $data['all_members_are_administrators'] ?? true;
    }
}