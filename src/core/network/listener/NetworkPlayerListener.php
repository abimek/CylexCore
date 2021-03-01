<?php
declare(strict_types=1);

namespace core\network\listener;

use core\main\base\BaseListener;
use core\main\text\message\Message;
use core\network\forms\VerifyForm;
use core\network\NetworkManager;
use core\players\objects\PlayerObject;
use core\players\PlayerManager;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\EffectManager;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;

class NetworkPlayerListener extends BaseListener
{

    public function onLogin(PlayerLoginEvent $event)
    {
        PlayerManager::getDatabaseHandler()->getPlayerObject($event->getPlayer()->getXuid(), function ($object) use ($event) {
            if ($object instanceof PlayerObject) {
                $player = $event->getPlayer();
                //$verified = NetworkManager::getNetworkPlayerDBHandler()->veryifyLogin($object);
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
                        return;
                    });
                    return;
                }
                $event->getPlayer()->kick(Message::PREFIX . "failed to login, this account is ip-locked, make a ticket on discord if this is your account.");
            } else {
                $event->getPlayer()->kick(Message::PREFIX . "failed to register account, please try again.");
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

    public function onquit(PlayerQuitEvent $event)
    {
        $xuid = $event->getPlayer()->getXuid();
        NetworkManager::getNetworkPlayerDBHandler()->saveAccount($xuid);
    }

    protected function init(): void
    {
        // TODO: Implement init() method.
    }
}