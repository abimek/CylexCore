<?php
declare(strict_types=1);

namespace core\admin\objects;

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
}