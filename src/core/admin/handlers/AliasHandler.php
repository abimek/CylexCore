<?php
declare(strict_types=1);

namespace core\admin\handlers;

use core\players\objects\PlayerObject;
use core\players\PlayerManager;

class AliasHandler
{

    public static function initAliases(PlayerObject $object)
    {
        $ip = $object->getIp();
        $name = $object->getUsername();
        foreach (PlayerManager::getDatabaseHandler()->getPlayerObjects() as $p) {
            if ($p instanceof PlayerObject) {
                if ($p->getIp() === $ip) {
                    $object->getBanData()->addAlias($p->getUsername());
                    $p->getBanData()->addAlias($name);
                }
            }
        }
    }

}