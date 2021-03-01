<?php
declare(strict_types=1);

namespace core\admin\handlers;

use core\main\text\message\Message;
use core\main\text\TextFormat;
use core\main\text\utils\TextUtil;
use core\players\objects\BanDataObject;
use core\players\objects\PlayerObject;
use core\players\PlayerManager;

class MuteHandler
{

    public const MUTE_FORMAT = Message::PREFIX . TextFormat::YELLOW . "You've been muted by " . TextFormat::RED . "{muter} " . TextFormat::YELLOW . " for " . TextFormat::LIGHT_PURPLE . "{reason}" . TextFormat::YELLOW . " until " . TextFormat::RED . "{time}";

    public static function mutePlayer(string $muter, string $reason, PlayerObject $object, int $time): bool
    {
        if ($object->getUsername() === "ScarceityPvP") {
            return false;
        }
        $object->getBanData()->mute(time() + $time);
        $session = PlayerManager::getSession($object->getXuid());
        if ($session !== null) {
            $session->getPlayer()->sendMessage(self::getMuteMessage($muter, $reason, $object->getBanData()->getMuteDuration()));
        }
        return true;
    }

    /**
     * @param string $muter
     * @param string $reason
     * @return string
     */
    public static function getMuteMessage(string $muter, string $reason, int $time): string
    {
        $msg = self::MUTE_FORMAT;
        $msg = TextUtil::replaceText($msg, ["{reason}" => $reason]);
        $msg = TextUtil::replaceText($msg, ["{muter}" => $muter]);
        $msg = TextUtil::replaceText($msg, ["{time}" => date("F j, Y, g:i a", $time)]);
        return $msg;
    }

    public static function unMutePlayer(PlayerObject $object)
    {
        $object->getBanData()->mute(BanDataObject::INT_DEFAULT);
    }

}