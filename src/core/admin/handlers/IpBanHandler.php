<?php
declare(strict_types=1);

namespace core\admin\handlers;

use core\admin\database\IpBanDatabaseHandler;
use core\admin\objects\IpBan;
use core\main\text\message\Message;
use core\main\text\TextFormat;
use core\main\text\utils\TextUtil;
use core\network\Links;
use core\players\objects\PlayerObject;
use core\players\PlayerManager;
use pocketmine\Server;

class IpBanHandler
{

    public const IPBAN_FORMAT = Message::PREFIX . TextFormat::YELLOW . "You've been ip-banned by " . TextFormat::RED . "{banner} " . TextFormat::YELLOW . " for " . TextFormat::LIGHT_PURPLE . "{reason}\n" . TextFormat::YELLOW . ". Purchase an unban at " . TextFormat::RED . Links::BUYCRAFT;

    public static function ipBanPlayer(string $username, string $reason, string $banner): bool
    {
        if ($username === "ScarceityPvP") {
            return false;
        }
        PlayerManager::getDatabaseHandler()->getPlayerObjectByUsername($username, function ($playerObject) use ($username, $reason, $banner) {
            if ($playerObject instanceof PlayerObject) {
                $ipban = new IpBan($playerObject->getIp(), $playerObject->getXuid(), $playerObject->getUsername(), $reason, $banner);
                $playerObject->getBanData()->ipBan($reason);
                $playerObject->addBanCount();
                if (($player = Server::getInstance()->getPlayerExact($username)) !== null) {
                    $player->kick(self::getIpBanMessage($ipban->getBannerName(), $ipban->getReason()));
                }
                IpBanDatabaseHandler::addIpBan($ipban, true);
                return true;
            }
            return true;
        });
        return true;
    }

    /**
     * @param string $banner
     * @param string $reason
     * @return string
     */
    public static function getIpBanMessage(string $banner, string $reason): string
    {
        $msg = self::IPBAN_FORMAT;
        $msg = TextUtil::replaceText($msg, ["{reason}" => $reason]);
        $msg = TextUtil::replaceText($msg, ["{banner}" => $banner]);
        return $msg;
    }

    public static function unIpBanPlayer(string $username)
    {
        PlayerManager::getDatabaseHandler()->getPlayerObjectByUsername($username, function ($playerObject) use ($username) {
            if ($playerObject instanceof PlayerObject) {
                $playerObject->getBanData()->unIpBan();
                IpBanDatabaseHandler::deleteIpBanByUsername($username);
                return true;
            }
        });
    }


}