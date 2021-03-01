<?php
declare(strict_types=1);

namespace core\admin\listeners;

use core\admin\forms\BanForm;
use core\admin\forms\player_info\PlayerInfoForm;
use core\admin\handlers\StaffModeHandler;
use core\admin\handlers\VanishHandler;
use core\main\base\BaseListener;
use core\main\text\message\Message;
use core\main\text\TextFormat;
use core\players\objects\PlayerObject;
use core\players\PlayerManager;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use pocketmine\Server;

class StaffModeListener extends BaseListener
{

    public function onDamage(EntityDamageByEntityEvent $event)
    {
        $damager = $event->getDamager();
        $entity = $event->getEntity();
        if ($damager instanceof Player) {
            if (VanishHandler::isVanished($damager)) {
                $item = $damager->getInventory()->getItemInHand();
                if ($entity instanceof Player) {
                    switch ($item->getId()) {
                        case ItemIds::BANNER:
                            PlayerManager::getDatabaseHandler()->getPlayerObject($entity->getXuid(), function ($playerObject) use ($damager) {
                                if ($playerObject instanceof PlayerObject) {
                                    $form = new PlayerInfoForm($damager, $playerObject);
                                    $damager->sendForm($form);
                                }
                            });
                            return;
                        case ItemIds::DIAMOND_AXE:
                            $form = new BanForm($damager, $entity->getName());
                            $damager->sendForm($form);
                    }
                }
                $event->cancel();
            }
        }
    }

    public function onPlace(BlockPlaceEvent $event)
    {
        $player = $event->getPlayer();
        if (StaffModeHandler::isInStaffMode($player)) {
            $event->cancel();
        }
    }

    public function interact(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        if (StaffModeHandler::isInStaffMode($player)) {
            $item = $event->getItem();
            $id = $item->getId();
            switch ($id) {
                case ItemIds::ARROW:
                    $players = Server::getInstance()->getOnlinePlayers();
                    $p = $players[array_rand($players)];
                    $player->teleport($p->getPosition());
                    $player->sendMessage(Message::PREFIX . "Teleporting to " . TextFormat::LIGHT_PURPLE . $p->getName());
                    return;
            }
        }
    }

    protected function init(): void
    {

    }


}