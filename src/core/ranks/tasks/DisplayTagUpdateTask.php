<?php
declare(strict_types=1);

namespace core\ranks\tasks;

use core\players\PlayerManager;
use core\ranks\RankHandler;
use core\ranks\RankManager;
use core\ranks\ranks\Rookie;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class DisplayTagUpdateTask extends Task
{

    public function __construct()
    {
    }

    /**
     * Actions to execute when run
     */
    public function onRun(): void
    {
        //TODO LATER

        $players = Server::getInstance()->getOnlinePlayers();
        foreach ($players as $player) {
            $session = PlayerManager::getSession($player->getXuid());
            if ($session === null) {
                return;
            }
            $rank_id = $session->getRankIdentifier();
            $rank = RankManager::getRank($rank_id);
            if ($rank === null) {
                RankHandler::setRank($session->getObject(), RankManager::getRank(Rookie::ROOKIE));
                $rank = RankManager::getRank(Rookie::ROOKIE);
            }
            $display = $rank->getDisplayTag();
            if ($display === null) {
                continue;
            }
            foreach (RankManager::getDisplayTagCallabes() as $callable) {
                $callable($display, $player->getXuid());
            }
            $player->setNameTag($display);
        }
    }
}