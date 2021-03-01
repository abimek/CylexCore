<?php
declare(strict_types=1);

namespace core\players\objects;

use core\database\related_objects\DatabaseObjectI;
use core\main\data\formatter\JsonFormatter;

class BanDataObject implements DatabaseObjectI
{

    use JsonFormatter;

    public const STRING_DEFAULT = "";
    public const INT_DEFAULT = -20;

    private $banned;
    private $ipBanned;
    private $tempBanned;
    private $mute;
    private $banReason;
    private $aliases = [];

    public function __construct(bool $banned, bool $ipBanned, int $tempBanned, int $mute, string $banReason)
    {
        $this->banned = $banned;
        $this->ipBanned = $ipBanned;
        $this->tempBanned = $tempBanned;
        $this->mute = $mute;
        $this->banReason = $banReason;
    }

    public static function createObjectFromData(array $data): BanDataObject
    {
        $ob = new BanDataObject($data["banned"], $data["ipBanned"], $data["tempBanned"], $data["mute"], $data["banReason"]);
        if (isset($data["aliases"])) {
            foreach ($data["aliases"] as $name) {
                $ob->addAlias($name);
            }
        }
        return $ob;
    }

    public function addAlias(string $name)
    {
        $this->aliases[$name] = $name;
    }

    public static function getDefaultBanData(): BanDataObject
    {
        return new BanDataObject(false, false, self::INT_DEFAULT, self::INT_DEFAULT, self::STRING_DEFAULT);
    }

    public function isBanned(): bool
    {
        return $this->banned;
    }

    public function isIpBanned(): bool
    {
        return $this->ipBanned;
    }

    public function isTempBanned(): bool
    {
        if ($this->tempBanned === self::INT_DEFAULT) {
            return false;
        }
        return true;
    }

    public function isMuted(): bool
    {
        if ($this->mute === self::INT_DEFAULT) {
            return false;
        }
        return true;
    }

    public function getBanReason(): string
    {
        return $this->banReason;
    }

    public function getTempBanDuration(): int
    {
        return $this->tempBanned;
    }

    public function getMuteDuration(): int
    {
        return $this->mute;
    }

    public function ban(string $reason): void
    {
        $this->banned = true;
        $this->banReason = $reason;
    }

    public function ipBan(string $reason): void
    {
        $this->ipBanned = true;
        $this->banReason = $reason;
    }

    public function unBan()
    {
        $this->banned = false;
        $this->banReason = "";
    }

    public function getAliases(): array
    {
        return $this->aliases;
    }

    public function unIpBan()
    {
        $this->ipBanned = false;
        $this->banReason = "";
    }

    public function mute(int $time)
    {
        $this->mute = $time;
    }

    public function encodeData(): string
    {
        $data = [
            "banned" => $this->banned,
            "ipBanned" => $this->ipBanned,
            "tempBanned" => $this->tempBanned,
            "mute" => $this->mute,
            "banReason" => $this->banReason,
            "aliases" => $this->aliases
        ];
        return $this->encodeJson($data);
    }

}