<?php
declare(strict_types=1);

namespace core\admin\database;

use core\admin\objects\Ban;
use core\database\DatabaseManager;
use core\database\objects\Query;

class BanDatabaseHandler
{

    private static $bans = [];
    private static $usernameMap = [];

    public function __construct()
    {
        $this->init();
    }

    private function init()
    {
        DatabaseManager::emptyQuery("CREATE TABLE IF NOT EXISTS bans(xuid VARCHAR(36) PRIMARY KEY , username TEXT, reason TEXT, banner_name TEXT);", 0, null);
    }

    /**
     * @param string $username
     */
    public static function deleteBanByUsername(string $username)
    {
        if (isset(self::$usernameMap[$username])) {
            $xuid = self::$usernameMap[$username];
            if (isset(self::$bans[$xuid])) {
                unset(self::$bans[$xuid]);
            }
        }
        DatabaseManager::emptyQuery("DELETE FROM bans WHERE username=?", Query::MAIN_DB, [$username]);
    }

    /**
     * @param string $xuid
     */
    public static function deleteBan(string $xuid)
    {
        if (isset(self::$bans[$xuid])) {
            unset(self::$bans[$xuid]);
        }
        DatabaseManager::emptyQuery("DELETE FROM bans WHERE xuid=?", Query::MAIN_DB, [$xuid]);
    }

    /**
     * @param string $name
     * @return Ban|null
     */
    public static function getBanByName(string $name): ?Ban
    {
        if (isset(self::$usernameMap[$name])) {
            return self::getBan(self::$usernameMap[$name]);
        }
        return null;
    }

    /**
     * @param string $xuid
     * @return Ban|null
     */
    public static function getBan(string $xuid): ?Ban
    {
        if (isset(self::$bans[$xuid])) {
            return self::$bans[$xuid];
        }
        return null;
    }

    public static function loadBanWithCallable(string $xuid, callable $callable)
    {
        if (isset(self::$bans[$xuid])) {
            $callable(self::$bans[$xuid]);
            return;
        }
        DatabaseManager::query("SELECT * FROM bans WHERE xuid=?", 0, [
            $xuid
        ], function ($result) use ($callable, $xuid) {
            foreach ($result as $row) {
                self::$bans[$row["xuid"]] = new Ban($row["xuid"], $row["username"], $row["reason"], $row["banner_name"]);
                self::$usernameMap[$row["username"]] = $row["xuid"];
            }
            if (isset(self::$bans[$xuid])) {
                $callable(self::$bans[$xuid]);
            }
        });
    }

    /**
     * @param Ban $ban
     * @param bool $update
     * @return bool
     */
    public static function addBan(Ban $ban, bool $update = false): bool
    {
        if (isset(self::$bans[$ban->getXuid()]) && $update === false) {
            return false;
        }
        self::$bans[$ban->getXuid()] = $ban;
        self::$usernameMap[$ban->getUsername()] = $ban->getXuid();
        if ($update) {
            DatabaseManager::emptyQuery("INSERT IGNORE INTO bans(xuid, username, reason, banner_name) VALUES (?, ?, ?, ?);", Query::MAIN_DB, [
                $ban->getXuid(),
                $ban->getUsername(),
                $ban->getReason(),
                $ban->getBannerName()
            ]);
            DatabaseManager::emptyQuery("UPDATE bans SET xuid=?, username=?, reason=?, banner_name=? WHERE xuid=?", Query::MAIN_DB, [
                $ban->getXuid(),
                $ban->getUsername(),
                $ban->getReason(),
                $ban->getBannerName(),
                $ban->getXuid()
            ]);
        } else {
            DatabaseManager::emptyQuery("INSERT IGNORE INTO bans(xuid, username, reason, banner_name) VALUES (?, ?, ?, ?);", Query::MAIN_DB, [
                $ban->getXuid(),
                $ban->getUsername(),
                $ban->getReason(),
                $ban->getBannerName()
            ]);
        }
        return true;
    }

    public function close()
    {
        foreach (self::$bans as $ban) {
            if ($ban instanceof Ban) {
              /**  DatabaseManager::emptyQuery("INSERT IGNORE INTO bans(xuid, username, reason, banner_name) VALUES (?, ?, ?, ?);", Query::MAIN_DB, [
                    $ban->getXuid(),
                    $ban->getUsername(),
                    $ban->getReason(),
                    $ban->getBannerName()
                ]);**/
              $ban->save();
            }
        }
    }
}