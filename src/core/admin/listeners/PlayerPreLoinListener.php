<?php
declare(strict_types=1);

namespace core\admin\listeners;

use core\admin\database\BanDatabaseHandler;
use core\admin\database\IpBanDatabaseHandler;
use core\admin\handlers\BanHandler;
use core\admin\handlers\IpBanHandler;
use core\admin\handlers\VanishHandler;
use core\admin\objects\Ban;
use core\admin\objects\IpBan;
use core\main\base\BaseListener;
use core\players\PlayerManager;
use core\ranks\RankManager;
use core\ranks\types\RankTypes;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerPreLoginEvent;

class PlayerPreLoinListener extends BaseListener
{


    public function preLogin(PlayerPreLoginEvent $event)
    {
        $player = $event->getPlayerInfo();
        $xuid = $player->getXuid();
        BanDatabaseHandler::loadBanWithCallable($xuid, function (Ban $ban) use ($event) {
            $event->setKickReason(PlayerPreLoginEvent::KICK_REASON_PLUGIN, BanHandler::getBanMessage($ban->getBannerName(), $ban->getReason()));
        });
        IpBanDatabaseHandler::loadIpBanWithCallableWithIp($event->getIp(), function (IpBan $ipban) use ($event) {
            $event->setKickReason(PlayerPreLoginEvent::KICK_REASON_PLUGIN, IpBanHandler::getIpBanMessage($ipban->getBannerName(), $ipban->getReason()));
        });
    }

    //Listener of Vanish
    public function playerJoinEvent(PlayerJoinEvent $event)
    {
        $session = PlayerManager::getSession($event->getPlayer()->getXuid());
        if ($session === null) {
            return;
        }
        $rank = RankManager::getRank($session->getObject()->getRank());
        if ($rank->getType() === RankTypes::NORMAL_RANK) {
            VanishHandler::vanishPlayersTo($session);
        }
    }

    protected function init(): void
    {

    }
}