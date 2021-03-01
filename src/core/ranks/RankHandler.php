<?php
declare(strict_types=1);

namespace core\ranks;

use core\players\objects\PlayerObject;
use core\players\PlayerManager;
use core\ranks\events\PlayerRankChangeEvent;

class RankHandler
{

    public static function setRank(PlayerObject $object, Rank $rank)
    {
        $object->setRank($rank->getIdentifier());
        $xuid = $object->getXuid();
        $session = PlayerManager::getSession($xuid);
        if ($session != null) {
            $session->setRank($rank->getIdentifier());
        }
        $event = new PlayerRankChangeEvent($object);
        $event->call();
    }

}