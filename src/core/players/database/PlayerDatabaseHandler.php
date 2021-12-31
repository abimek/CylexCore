<?php
declare(strict_types=1);

namespace core\players\database;

use core\admin\handlers\AliasHandler;
use core\CylexCore;
use core\database\DatabaseManager;
use core\database\objects\Query;
use core\main\data\formatter\JsonFormatter;
use core\main\text\message\Message;
use core\main\text\TextFormat;
use core\network\NetworkManager;
use core\players\listener\PlayerListener;
use core\players\objects\BanDataObject;
use core\players\objects\PlayerObject;
use core\players\PlayerManager;
use core\ranks\ranks\Rookie;
use pocketmine\player\Player;
use pocketmine\Server;

final class PlayerDatabaseHandler
{
    use JsonFormatter;

    private $players = [];
    private $players_by_username = [];
    private $table_name = "";

    private static $instance;

    private static $waitingCallables = [];

    private $queryLists;

    public function __construct()
    {
        $this->init();
        self::$instance = $this;
    }

    public function init()
    {
        $this->table_name = CylexCore::getInstance()->getConfig()->get("PlayerTableName");
        $t = $this->table_name;
        DatabaseManager::emptyQuery("CREATE TABLE IF NOT EXISTS {$t}(xuid VARCHAR(36) PRIMARY KEY, username TEXT, ip TEXT, rank TEXT, ban_count INTEGER, ban_data TEXT)", Query::SERVER_DB, []);
    }

    public static function getTableName(): string {
        return self::$instance->table_name;
    }

    public function getPlayerObjects(): array
    {
        return $this->players;
    }

    /**
     * @param Player $player
     */
    public function createObject(Player $player, ?callable $callable = null)
    {
        $xuid = $player->getXuid();
        $ip = $player->getNetworkSession()->getIp();
        $username = $player->getName();
        if (isset($this->players[intval($xuid)])) {
            NetworkManager::getNetworkPlayerDBHandler()->createAccount($xuid, $username, $ip, false, false, "", "", "", "", []);
            if ($callable !== null){
                $callable($this->players[intval($xuid)]);
            }
            return;
        }
        DatabaseManager::query("SELECT * FROM {$this->table_name} WHERE xuid=?", Query::SERVER_DB, [$xuid], function ($results) use ($xuid, $username, $ip, $player, $callable) {
            foreach ($results as $player_data) {
                if (isset($player_data["ban_data"])) {
                    $ban_data = $this->decodeJson($player_data["ban_data"]);
                    $banObject = BanDataObject::createObjectFromData($ban_data);
                    $username = $player_data["username"];
                    $ip = $player_data["ip"];
                    $rank = $player_data["rank"];
                    $ban_count = $player_data["ban_count"];
                } else {
                    $rank = Rookie::ROOKIE;
                    $ban_count = 0;
                    $banObject = new BanDataObject(false, false, BanDataObject::INT_DEFAULT, BanDataObject::INT_DEFAULT, BanDataObject::STRING_DEFAULT);
                }
                $playerObject = new PlayerObject($banObject, $xuid, $username, $ip, $rank, $ban_count);
                $this->players[intval($xuid)] = $playerObject;
                if ($callable !== null) {
                    $callable($this->players[intval($xuid)]);
                }
                $this->players_by_username[$username] = $xuid;
                NetworkManager::getNetworkPlayerDBHandler()->createAccount($xuid, $username, $ip, false, false, "", "", "", "", []);
                return;
            }
            Server::getInstance()->broadcastMessage(Message::PREFIX . TextFormat::LIGHT_PURPLE . $username . TextFormat::GRAY . " joined for the first time!");
            $this->players_by_username[$username] = $xuid;
            $rank = Rookie::ROOKIE;
            $ban_count = 0;
            $ban_data = BanDataObject::getDefaultBanData();
            $playerObject = new PlayerObject($ban_data, $xuid, $username, $ip, $rank, $ban_count);
            PlayerManager::createSession($player, $playerObject, PlayerListener::wantsGui($player->getUniqueId()->toString()));
            $this->players[$xuid] = $playerObject;
            DatabaseManager::emptyQuery("INSERT IGNORE INTO {$this->table_name}(xuid, username, ip, rank, ban_count, ban_data) VALUES (?, ?, ?, ?, ?, ?);", Query::SERVER_DB, [
                $playerObject->getXuid(),
                $playerObject->getUsername(),
                $playerObject->getIp(),
                $playerObject->getRank(),
                $playerObject->getBanCount(),
                $playerObject->getBanData()->encodeData()
            ]);
            AliasHandler::initAliases($this->players[$xuid]);
            NetworkManager::getNetworkPlayerDBHandler()->createAccount($xuid, $username, $ip, false, false, "", "", "", "", []);
        });
    }


    public function getPlayerObjectByUsername(string $username, callable $callable)
    {
        if (isset($this->players_by_username[$username])) {
            $callable($this->players[$this->players_by_username[$username]]);
            return;
        }
        DatabaseManager::query("SELECT * FROM {$this->table_name} WHERE username=?", Query::SERVER_DB, [$username], function ($result) use ($callable) {
            foreach ($result as $player_data) {
                $xuid = $player_data["xuid"];
                $ban_data = $this->decodeJson($player_data["ban_data"]);
                $banObject = BanDataObject::createObjectFromData($ban_data);
                $playerObject = new PlayerObject($banObject, $xuid, $player_data["username"], $player_data["ip"], $player_data["rank"], $player_data["ban_count"]);
                $this->players[strval($xuid)] = $playerObject;
                $this->players_by_username[$player_data["username"]] = $xuid;
                $callable($playerObject);
                return;
            }
            $callable(null);
        });
    }

    /**
     * @param string $xuid
     * @param callable $callable
     * @return void
     */
    public function getPlayerObject(string $xuid, callable $callable): void
    {
        if (isset($this->players[$xuid])) {
            $callable($this->players[$xuid]);
        }
        DatabaseManager::query("SELECT * FROM {$this->table_name} WHERE xuid=?", Query::SERVER_DB, [$xuid], function ($result) use ($callable, $xuid) {
            foreach ($result as $player_data) {
                $xuid = $player_data["xuid"];
                $ban_data = $this->decodeJson($player_data["ban_data"]);
                $banObject = BanDataObject::createObjectFromData($ban_data);
                $playerObject = new PlayerObject($banObject, $xuid, $player_data["username"], $player_data["ip"], $player_data["rank"], $player_data["ban_count"]);
                $this->players[strval($xuid)] = $playerObject;
                $this->players_by_username[$player_data["username"]] = $xuid;
                $callable($playerObject);
                return;
            }
            $playerObject = $this->players[intval($xuid)];
            if ($playerObject instanceof PlayerObject) {
                $callable($playerObject);
            }
        });
    }

    public function savePlayer(PlayerObject $object)
    {
        DatabaseManager::emptyQuery("UPDATE {$this->table_name} SET username=?, ip=?, rank=?, ban_count=?, ban_data=? WHERE xuid=?", Query::SERVER_DB, [
            $object->getUsername(),
            $object->getIp(),
            $object->getRank(),
            $object->getBanCount(),
            $object->getBanData()->encodeData(),
            $object->getXuid()
        ]);
    }

    public function close()
    {
        $t = $this->table_name;
        foreach ($this->players as $xuid => $playerObject) {
            if ($playerObject instanceof PlayerObject) {
                $playerObject->save();
          /**      DatabaseManager::emptyQuery("INSERT IGNORE INTO {$t}(xuid, username, ip, rank, ban_count, ban_data) VALUES (?, ?, ?, ?, ?, ?);", Query::SERVER_DB, [
                    $playerObject->getXuid(),
                    $playerObject->getUsername(),
                    $playerObject->getIp(),
                    $playerObject->getRank(),
                    $playerObject->getBanCount(),
                    $playerObject->getBanData()->encodeData()
                ]);**/
            }
        }
    }
}