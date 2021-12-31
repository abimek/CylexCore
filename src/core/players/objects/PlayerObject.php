<?php
declare(strict_types=1);

namespace core\players\objects;

use core\database\DatabaseManager;
use core\database\objects\Query;
use core\players\database\PlayerDatabaseHandler;

class PlayerObject
{

    private $xuid;
    private $username;
    private $rank;
    private $ban_count;
    private $ip;

    private $banData;

    public function __construct(BanDataObject $banData, string $xuid, string $username, string $ip, string $rank, int $ban_count)
    {
        $this->banData = $banData;
        $this->xuid = $xuid;
        $this->username = $username;
        $this->ip = $ip;
        $this->rank = $rank;
        $this->ban_count = $ban_count;
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
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @return string
     */
    public function getRank(): string
    {
        return $this->rank;
    }

    /**
     * @param string $identifier
     */
    public function setRank(string $identifier)
    {
        $this->rank = $identifier;
        $this->save();
    }

    /**
     * @return BanDataObject
     */
    public function getBanData(): BanDataObject
    {
        return $this->banData;
    }

    /**
     * @return int
     */
    public function getBanCount(): int
    {
        return $this->ban_count;
    }

    /**
     *
     */
    public function addBanCount(): void
    {
        $this->ban_count++;
        $this->save();
    }

    public function save(){
        $t = PlayerDatabaseHandler::getTableName();
        $playerObject = $this;
        DatabaseManager::emptyQuery("UPDATE {$t} SET xuid=?, username=?, ip=?, rank=?, ban_count=?, ban_data=? WHERE xuid=?", Query::SERVER_DB, [
            $playerObject->getXuid(),
            $playerObject->getUsername(),
            $playerObject->getIp(),
            $playerObject->getRank(),
            $playerObject->getBanCount(),
            $playerObject->getBanData()->encodeData(),
            $playerObject->getXuid()
        ]);
    }

    /**
     * @return array
     */
    public function encodeData(): array
    {
        return [
            "username" => $this->username,
            "ip" => $this->ip,
            "rank" => $this->rank,
            "ban_count" => $this->ban_count,
            "ban_data" => $this->banData->encodeData()
        ];
    }
}