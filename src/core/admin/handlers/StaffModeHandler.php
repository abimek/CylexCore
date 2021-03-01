<?php
declare(strict_types=1);

namespace core\admin\handlers;

use core\main\text\message\Message;
use core\main\text\TextFormat;
use core\players\session\PlayerSession;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class StaffModeHandler
{

    private static $staffModePlayers;

    public static function toggleStaffMode(PlayerSession $session)
    {
        if (isset(self::$staffModePlayers[$session->getObject()->getXuid()])) {
            self::disableStaffMode($session);
        } else {
            self::enableStaffMode($session);
        }
    }

    private static function disableStaffMode(PlayerSession $session)
    {
        $player = $session->getPlayer();
        unset(self::$staffModePlayers[$session->getObject()->getXuid()]);
        $player->sendMessage(Message::PREFIX . "Successfully disabled staff-mode");
        $player = $session->getPlayer();
        $player->getInventory()->clearAll();
    }

    private static function enableStaffMode(PlayerSession $session)
    {
        self::$staffModePlayers[$session->getObject()->getXuid()] = $session;
        $player = $session->getPlayer();
        $player->sendMessage(Message::PREFIX . "Successfully enabled staff-mode");
        VanishHandler::vanishPlayer($player);
        $player->getInventory()->clearAll();
        $player->getInventory()->setContents(self::getAdminInventoryContents());
        $player->setAllowFlight(true);
    }

    private static function getAdminInventoryContents(): array
    {
        return [
            0 => ItemFactory::getInstance()->get(ItemIds::DIAMOND_AXE)->setCustomName(TextFormat::BOLD_LIGHT_PURPLE . "Ban"),
            4 => ItemFactory::getInstance()->get(ItemIds::ARROW)->setCustomName(TextFormat::BOLD_LIGHT_PURPLE . "Teleport To Random Player"),
            8 => ItemFactory::getInstance()->get(ItemIds::BANNER)->setCustomName(TextFormat::BOLD_LIGHT_PURPLE . "Info")
        ];
    }

    public static function isInStaffMode(Player $player): bool
    {
        return isset(self::$staffModePlayers[$player->getXuid()]);
    }

}