<?php
declare(strict_types=1);

namespace core\admin\database;

use core\admin\objects\Ban;
use core\admin\objects\IpBan;
use core\database\DatabaseManager;
use core\database\objects\Query;

class IpBanDatabaseHandler
{

    private static $ipbans = [];
    private static $usernameMap = [];
    private static $ipMap = [];

    public function __construct()
    {
        $this->init();
    }

    private function init()
    {
        DatabaseManager::emptyQuery("CREATE TABLE IF NOT EXISTS ipbans(ip VARCHAR(20) PRIMARY KEY , xuid TEXT, username TEXT, reason TEXT, banner_name TEXT);", Query::SERVER_DB, null);
    }

    /**
     * @param string $username
     */
    public static function deleteIpBanByUsername(string $username)
    {
        if (isset(self::$usernameMap[$username])) {
            $xuid = self::$usernameMap[$username];
            self::deleteIpBanByXuid($xuid);
        }
    }

    /**
     * @param string $xuid
     */
    public static function deleteIpBanByXuid(string $xuid)
    {
        if (isset(self::$ipbans[$xuid])) {
            unset(self::$ipbans[$xuid]);
            DatabaseManager::emptyQuery("DELETE FROM ipbans WHERE xuid=?", Query::SERVER_DB, [$xuid]);
        }
    }

    /**
     * @param string $name
     * @return Ban|null
     */
    public static function getIpBanByName(string $name): ?IpBan
    {
        if (isset(self::$usernameMap[$name])) {
            return self::getIpBan(self::$usernameMap[$name]);
        }
        return null;
    }

    /**
     * @param string $xuid
     * @return Ban|null
     */
    public static function getIpBan(string $xuid): ?IpBan
    {
        if (isset(self::$ipbans[$xuid])) {
            return self::$ipbans[$xuid];
        }
        return null;
    }

    public static function loadIpBanWithCallableWithIp(string $ip, callable $callable): void
    {
        if (self::getIpBanByIp($ip) !== null) {
            $callable(self::getIpBanByIp($ip));
            return;
        }
        DatabaseManager::query("SELECT * FROM ipbans WHERE ip=?", Query::SERVER_DB, [$ip], function ($results) use ($callable) {
            foreach ($results as $row) {
                $ipban = new IpBan($row["ip"], $row["xuid"], $row["username"], $row["reason"], $row["banner_name"]);
                $xuid = $row["xuid"];
                self::$ipbans[$xuid] = $ipban;
                self::$usernameMap[$row["username"]] = $xuid;
                self::$ipMap[$row["ip"]] = $xuid;
                $callable($ipban);
            }
        });
    }

    public static function getIpBanByIp(string $ip): ?IpBan
    {
        if (isset(self::$ipMap[$ip])) {
            return self::getIpBan(self::$ipMap[$ip]);
        }
        return null;
    }

    /**
     * @param IpBan $ipban
     * @param bool $update
     * @return bool
     */
    public static function addIpBan(IpBan $ipban, bool $update = false): bool
    {
        if (isset(self::$ipbans[$ipban->getXuid()]) && $update === false) {
            return false;
        }
        self::$ipbans[$ipban->getXuid()] = $ipban;
        self::$usernameMap[$ipban->getUsername()] = $ipban->getXuid();
        if ($update) {
            DatabaseManager::emptyQuery("INSERT IGNORE INTO ipbans(ip, xuid, username, reason, banner_name) VALUES (?, ?, ?, ?, ?);", Query::SERVER_DB, [
                $ipban->getIp(),
                $ipban->getXuid(),
                $ipban->getUsername(),
                $ipban->getReason(),
                $ipban->getBannerName()
            ]);
            DatabaseManager::emptyQuery("UPDATE ipbans SET ip=?, xuid=?, username=?, reason=?, banner_name=? WHERE xuid=?", Query::SERVER_DB, [
                $ipban->getIp(),
                $ipban->getXuid(),
                $ipban->getUsername(),
                $ipban->getReason(),
                $ipban->getBannerName(),
                $ipban->getXuid()
            ]);
        } else {
            DatabaseManager::emptyQuery("INSERT IGNORE INTO ipbans(ip, xuid, username, reason, banner_name) VALUES (?, ?, ?, ?, ?);", Query::SERVER_DB, [
                $ipban->getIp(),
                $ipban->getXuid(),
                $ipban->getUsername(),
                $ipban->getReason(),
                $ipban->getBannerName()
            ]);
        }
        return true;
    }

    public function close()
    {
        foreach (self::$ipbans as $ipban) {
            if ($ipban instanceof IpBan) {
               /** DatabaseManager::emptyQuery("INSERT IGNORE INTO bans(ip, xuid, username, reason, banner_name) VALUES (?, ?, ?, ?, ?);", Query::SERVER_DB, [
                    $ipban->getIp(),
                    $ipban->getXuid(),
                    $ipban->getUsername(),
                    $ipban->getReason(),
                    $ipban->getBannerName()
                ]);**/
                $ipban->save();
            }
        }
    }
}
