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
use core\main\text\message\Message;
use core\network\forms\VerifyForm;
use core\network\NetworkManager;
use core\players\objects\PlayerObject;
use core\players\PlayerManager;
use pocketmine\entity\EffectInstance;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\network\mcpe\protocol\types\DeviceOS;
use pocketmine\Player;

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
        $handler->createObject($player, function ($object) use ($player, $event) {
            if ($object instanceof PlayerObject) {
                $player = $event->getPlayer();
                $verified = true;
                if ($verified === true) {
                    NetworkManager::getNetworkPlayerDBHandler()->loadAccountAndCallable($object->getXuid(), function ($networkData) use ($player) {
                        if ($networkData->isPasswordLocked()) {
                            $manager = $player->getEffects();
                            $effect = new EffectInstance(VanillaEffects::BLINDNESS(), 10000000, 100, false);
                            $manager->add($effect);
                            $effect = new EffectInstance(VanillaEffects::SLOWNESS(), 100000, 100, false);
                            $manager->add($effect);
                            $this->sendVerifyForm($player, $manager);
                        }
                    });
                }
            } else {
                $event->getPlayer()->kick(Message::PREFIX . "failed to register account, please try again.");
            }
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

    private function sendVerifyForm(Player $player, EffectManager $manager)
    {
        $player->setInvisible(true);
        $form = new VerifyForm($player, function (Player $player) use ($manager) {
            $player->setInvisible(false);
            $manager->remove(VanillaEffects::BLINDNESS());
            $manager->remove(VanillaEffects::SLOWNESS());
            $player->sendMessage(Message::PREFIX . "Successfully logged in!");
        }, function (Player $player) use ($manager) {
            $this->sendVerifyForm($player, $manager);
        });
        $player->sendForm($form);
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
