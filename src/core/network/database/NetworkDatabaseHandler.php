<?php
declare(strict_types=1);

namespace core\network\database;

use core\database\DatabaseManager;
use core\database\objects\Query;
use core\main\data\formatter\BooleanFormatter;
use core\main\data\formatter\JsonFormatter;
use core\network\objects\NetworkPlayer;
use core\players\objects\PlayerObject;
use core\players\session\PlayerSession;

final class NetworkDatabaseHandler
{
    use JsonFormatter;
    use BooleanFormatter;

    private $network_players = [];
    private $players_by_username = [];

    private $queryLists;

    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        DatabaseManager::emptyQuery("CREATE TABLE IF NOT EXISTS network_players(xuid VARCHAR(36) PRIMARY KEY, username TEXT, ip TEXT, ip_locked INTEGER , password_locked INTEGER, password TEXT, discord TEXT, youtube TEXT,  description TEXT, mail TEXT)", Query::MAIN_DB);
    }

    /**
     * @param PlayerObject $object
     * @return bool
     */
    public function veryifyLogin(PlayerObject $object): bool
    {
        $xuid = $object->getXuid();
        if (($network_player = $this->getPlayerObject($xuid)) !== null) {
            if ($network_player->isIpLocked()) {
                if ($object->getIp() !== $network_player->getIp()) {
                    return false;
                }
            }
            return true;
        }
        return true;
    }

    /**
     * @param string $xuid
     * @return PlayerObject|null
     */
    public function getPlayerObject(string $xuid): ?NetworkPlayer
    {
        if (isset($this->network_players[$xuid])) {
            return $this->network_players[$xuid];
        }
        return null;
    }

    public function loadAccountAndCallable(string $xuid, callable $callable)
    {
        if (isset($this->network_players[$xuid])) {
            $callable($this->network_players[$xuid]);
            return;
        }
        DatabaseManager::query("SELECT * FROM network_players WHERE xuid=?", Query::MAIN_DB, [$xuid], function ($result) use ($callable) {
            foreach ($result as $value) {
                $player = new NetworkPlayer($value["xuid"], $value["username"], $value["ip"], $this->decodeBool($value["ip_locked"]), $this->decodeBool($value["password_locked"]), $value["password"], $value["discord"], $value["youtube"], $value["description"], $this->decodeJson($value["mail"]));
                if (!isset($this->network_players[$value["xuid"]])) {
                    $this->network_players[$value["xuid"]] = $player;
                    $this->players_by_username[$value["username"]] = $value["xuid"];
                    $callable($player);
                }
            }
        });
    }

    public function deleteAccount(PlayerSession $session)
    {
        $networkPlayer = $this->getPlayerObject($session->getObject()->getXuid());
        if ($networkPlayer === null) {
            return;
        }
        DatabaseManager::emptyQuery("UPDATE network_players SET xuid=?, username=?, ip=?, ip_locked=?, password_locked=?, password=?, discord=?, youtube=?, description=?, mail=? WHERE xuid=?", Query::MAIN_DB, [
            $networkPlayer->getXuid(),
            $networkPlayer->getUsername(),
            $networkPlayer->getIp(),
            $this->encodeBool($networkPlayer->isIpLocked()),
            $this->encodeBool($networkPlayer->isPasswordLocked()),
            $networkPlayer->getPassword(),
            $networkPlayer->getDiscord(),
            $networkPlayer->getYoutube(),
            $networkPlayer->getDescription(),
            $this->encodeJson($networkPlayer->getMail()),
            $networkPlayer->getXuid(),
        ]);
        unset($this->network_players[$session->getObject()->getXuid()]);
        unset($this->players_by_username[$session->getPlayer()->getName()]);
    }

    public function createAccount(string $xuid, string $username, string $ip, bool $ip_locked, bool $password_locked, string $password, string $discord, string $youtube, string $description, array $mail = [])
    {
        DatabaseManager::query("INSERT IGNORE INTO network_players(xuid, username, ip, ip_locked, password_locked, password, discord, youtube, description, mail) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?);", Query::MAIN_DB, [
            $xuid,
            $username,
            $ip,
            $this->encodeBool($ip_locked),
            $this->encodeBool($password_locked),
            $password,
            $discord,
            $youtube,
            $description,
            $this->encodeJson($mail)
        ], function ($result) use ($xuid) {
            DatabaseManager::query("SELECT * FROM network_players WHERE xuid=?", Query::MAIN_DB, [$xuid], function ($result) {
                foreach ($result as $value) {
                    $player = new NetworkPlayer($value["xuid"], $value["username"], $value["ip"], $this->decodeBool($value["ip_locked"]), $this->decodeBool($value["password_locked"]), $value["password"], $value["discord"], $value["youtube"], $value["description"], $this->decodeJson($value["mail"]));
                    if (!isset($this->network_players[$value["xuid"]])) {
                        $this->network_players[$value["xuid"]] = $player;
                        $this->players_by_username[$value["username"]] = $value["xuid"];
                    }
                }
            });
        });
    }

    public function saveAccount(string $xuid)
    {
        if (isset($this->network_players[$xuid])) {
            $networkPlayer = $this->network_players[$xuid];
            if ($networkPlayer instanceof NetworkPlayer) {
                DatabaseManager::emptyQuery("UPDATE network_players SET xuid=?, username=?, ip=?, ip_locked=?, password_locked=?, password=?, discord=?, youtube=?, description=?, mail=? WHERE xuid=?", Query::MAIN_DB, [
                    $networkPlayer->getXuid(),
                    $networkPlayer->getUsername(),
                    $networkPlayer->getIp(),
                    $this->encodeBool($networkPlayer->isIpLocked()),
                    $this->encodeBool($networkPlayer->isPasswordLocked()),
                    $networkPlayer->getPassword(),
                    $networkPlayer->getDiscord(),
                    $networkPlayer->getYoutube(),
                    $networkPlayer->getDescription(),
                    $this->encodeJson($networkPlayer->getMail()),
                    $networkPlayer->getXuid(),
                ]);
                $username = $networkPlayer->getUsername();
                unset($this->players_by_username[$username]);
                unset($this->network_players[$xuid]);
            }
        }
    }

    /**
     * @param string $username
     * @return PlayerObject|null
     */
    public function getPlayerObjectByUsername(string $username): ?NetworkPlayer
    {
        if (!isset($this->players_by_username[$username])) {
            return null;
        }
        return $this->getPlayerObject($this->players_by_username[$username]);
    }

    public function close()
    {
        foreach ($this->network_players as $xuid => $networkPlayer) {
            if ($networkPlayer instanceof NetworkPlayer) {
               $networkPlayer->save();
            }
        }
    }
}