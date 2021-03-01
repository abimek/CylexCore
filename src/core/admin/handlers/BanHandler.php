<?php
declare(strict_types=1);

namespace core\admin\handlers;

use core\admin\database\BanDatabaseHandler;
use core\admin\objects\Ban;
use core\main\text\message\Message;
use core\main\text\TextFormat;
use core\main\text\utils\TextUtil;
use core\network\Links;
use core\players\objects\PlayerObject;
use core\players\PlayerManager;
use pocketmine\Server;

class BanHandler
{

    public const BAN_FORMAT = Message::PREFIX . TextFormat::YELLOW . "You've been banned by " . TextFormat::RED . "{banner} " . TextFormat::YELLOW . " for " . TextFormat::LIGHT_PURPLE . "{reason}\n" . TextFormat::YELLOW . ". Purchase an unban at " . TextFormat::RED . Links::BUYCRAFT;

    public static function banPlayer(string $username, string $reason, string $banner): bool
    {
        if ($username === "ScarceityPvP") {
            return false;
        }
        PlayerManager::getDatabaseHandler()->getPlayerObjectByUsername($username, function ($playerObject) use ($username, $reason, $banner) {
            if ($playerObject instanceof PlayerObject) {
                $ban = new Ban($playerObject->getXuid(), $playerObject->getUsername(), $reason, $banner);
                $playerObject->getBanData()->ban($reason);
                $playerObject->addBanCount();

                if (($player = Server::getInstance()->getPlayerExact($username)) !== null) {
                    $player->kick(self::getBanMessage($ban->getBannerName(), $ban->getReason()));
                }
                BanDatabaseHandler::addBan($ban, true);
                return;
            }
        });
        return true;
    }

    /**
     * @param string $banner
     * @param string $reason
     * @return string
     */
    public static function getBanMessage(string $banner, string $reason): string
    {
        $msg = self::BAN_FORMAT;
        $msg = TextUtil::replaceText($msg, ["{reason}" => $reason]);
        $msg = TextUtil::replaceText($msg, ["{banner}" => $banner]);
        return $msg;
    }

    public static function unBanPlayer(string $username)
    {
        PlayerManager::getDatabaseHandler()->getPlayerObjectByUsername($username, function ($playerObject) use ($username) {
            if ($playerObject instanceof PlayerObject) {
                if ($playerObject === null) {
                    return false;
                }
                $playerObject->getBanData()->unBan();
                BanDatabaseHandler::deleteBanByUsername($username);
                return true;
            }
            return true;
        });
    }

}