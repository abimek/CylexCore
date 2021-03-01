<?php
declare(strict_types=1);

namespace core\admin\handlers;

use core\main\text\message\Message;
use core\main\text\TextFormat;
use core\players\PlayerManager;
use core\players\session\PlayerSession;
use core\ranks\RankManager;
use core\ranks\types\RankTypes;
use pocketmine\player\Player;

class VanishHandler
{

    private static $vanishes = [];

    public static function toggleVanish(PlayerSession $session)
    {
        $xuid = $session->getObject()->getXuid();
        if (isset(self::$vanishes[$xuid])) {
            unset(self::$vanishes[$xuid]);
            $session->getPlayer()->sendMessage(Message::PREFIX . "exited " . TextFormat::LIGHT_PURPLE . "vanish-mode!");
            self::unVanishPlayer($session->getPlayer());
            return;
        } else {
            self::$vanishes[$xuid] = $session;
            $session->getPlayer()->sendMessage(Message::PREFIX . "entered " . TextFormat::LIGHT_PURPLE . "vanish-mode!");
            self::vanishPlayer($session->getPlayer());
        }
    }

    public static function unVanishPlayer(Player $player)
    {
        foreach (PlayerManager::getSessions() as $session) {
            if ($session instanceof PlayerSession) {
                $rank = RankManager::getRank($session->getObject()->getRank());
                if ($rank->getType() === RankTypes::STAFF_RANK && $session->getPlayer()->getName() !== $player->getName()) {
                    $session->getPlayer()->sendMessage(Message::PREFIX . $player->getName() . " unvanished!");
                }
                $session->getPlayer()->showPlayer($player);
            }
        }
    }

    public static function vanishPlayer(Player $player)
    {
        foreach (PlayerManager::getSessions() as $session) {
            if ($session instanceof PlayerSession) {
                $rank = RankManager::getRank($session->getObject()->getRank());
                if ($rank->getType() === RankTypes::STAFF_RANK && $session->getPlayer()->getName() !== $player->getName()) {
                    $session->getPlayer()->sendMessage(Message::PREFIX . $player->getName() . " vanished!");
                    continue;
                }
                $session->getPlayer()->showPlayer($player);
            }
        }
    }

    public static function isVanished(Player $player)
    {
        return isset(self::$vanishes[$player->getXuid()]);
    }

    public static function vanishPlayersTo(PlayerSession $session)
    {
        if (RankManager::getRank($session->getObject()->getRank())->getType() === RankTypes::STAFF_RANK) {
            return;
        }
        foreach (self::$vanishes as $xuid => $session1) {
            if ($session1 instanceof PlayerSession) {
                $session->getPlayer()->hidePlayer($session1->getPlayer());
            }
        }
    }
}