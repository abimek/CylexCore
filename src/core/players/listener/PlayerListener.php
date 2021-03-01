<?php
declare(strict_types=1);

namespace core\players\listener;

use core\admin\database\BanDatabaseHandler;
use core\admin\database\IpBanDatabaseHandler;
use core\admin\handlers\BanHandler;
use core\admin\handlers\IpBanHandler;
use core\admin\objects\Ban;
use core\admin\objects\IpBan;
use core\main\base\BaseListener;
use core\network\NetworkManager;
use core\players\objects\PlayerObject;
use core\players\PlayerManager;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\network\mcpe\protocol\types\DeviceOS;

final class PlayerListener extends BaseListener
{

    /**
     * @var PlayerListener
     */
    private static $instance;
    private $wantsguidata = [];

    public static function wantsGui($uuid)
    {
        if (isset(self::$instance->wantsguidata[$uuid])) {
            return self::$instance->wantsguidata[$uuid];
        } else {
            return true;
        }
    }

    public function preLogin(PlayerPreLoginEvent $event)
    {
        if ($event->getPlayerInfo()->getExtraData()["DeviceOS"] === DeviceOS::WINDOWS_10) {
            $this->wantsguidata[$event->getPlayerInfo()->getUuid()->toString()] = true;
        }
        $this->wantsguidata[$event->getPlayerInfo()->getUuid()->toString()] = false;
    }

    public function onLogin(PlayerLoginEvent $event)
    {
        $player = $event->getPlayer();
        $handler = PlayerManager::getDatabaseHandler();
        $handler->createObject($player);
        $handler->getPlayerObject($player->getXuid(), function ($object) use ($player, $event) {
            if ($object instanceof PlayerObject) {
                PlayerManager::createSession($player, $object, $this->wantsguidata[$player->getUniqueId()->toString()]);
                $xuid = $player->getXuid();
                BanDatabaseHandler::loadBanWithCallable($xuid, function (Ban $ban) use ($event) {
                    $event->getPlayer()->kick(BanHandler::getBanMessage($ban->getBannerName(), $ban->getReason()));
                });
                IpBanDatabaseHandler::loadIpBanWithCallableWithIp($event->getPlayer()->getNetworkSession()->getIp(), function (IpBan $ipban) use ($event) {
                    $event->getPlayer()->kick(ipBanHandler::getIpBanMessage($ipban->getBannerName(), $ipban->getReason()));
                });
            }
        });
    }

    public function onLeave(PlayerQuitEvent $event)
    {
        if (($session = PlayerManager::getSession($event->getPlayer()->getXuid())) !== null) {
            NetworkManager::getNetworkPlayerDBHandler()->deleteAccount(PlayerManager::getSession($event->getPlayer()->getXuid()));
            PlayerManager::deleteSession($event->getPlayer());
        }
    }

    protected function init(): void
    {
        self::$instance = $this;
    }
}
