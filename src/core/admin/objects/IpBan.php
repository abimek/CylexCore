<?php
declare(strict_types=1);

namespace core\admin\objects;

use core\database\DatabaseManager;
use core\database\objects\Query;

class IpBan extends Ban
{

    private $ip;

    public function __construct(string $ip, string $xuid, string $username, string $reason, string $banner_name)
    {
        $this->ip = $ip;
        parent::__construct($xuid, $username, $reason, $banner_name);
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function save(){
        $ipban = $this;
        DatabaseManager::emptyQuery("UPDATE bans SET ip=?, xuid=?, username=?, reason=?, banner_name=? WHERE xuid=?", Query::SERVER_DB, [
            $ipban->getIp(),
            $ipban->getXuid(),
            $ipban->getUsername(),
            $ipban->getReason(),
            $ipban->getBannerName(),
            $ipban->getXuid()
        ]);
    }
}