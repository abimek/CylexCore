<?php
declare(strict_types=1);

namespace core\admin\objects;

use core\database\DatabaseManager;
use core\database\objects\Query;

class Ban
{

    private $xuid;
    private $username;
    private $reason;
    private $banner_name;

    public function __construct(string $xuid, string $username, string $reason, string $banner_name)
    {
        $this->xuid = $xuid;
        $this->username = $username;
        $this->reason = $reason;
        $this->banner_name = $banner_name;
    }

    /**
     * @return string
     */
    public function getXuid(): string
    {
        return $this->xuid;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getReason(): string
    {
        return $this->reason;
    }

    /**
     * @return string
     */
    public function getBannerName(): string
    {
        return $this->banner_name;
    }

    public function save(){
        $ban = $this;
        DatabaseManager::emptyQuery("UPDATE bans SET xuid=?, username=?, reason=?, banner_name=? WHERE xuid=?", Query::MAIN_DB, [
            $ban->getXuid(),
            $ban->getUsername(),
            $ban->getReason(),
            $ban->getBannerName(),
            $ban->getXuid()
        ]);
    }
}