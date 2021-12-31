<?php
declare(strict_types=1);

namespace core\main\managers;

use core\CylexCore;
use Exception;
use pocketmine\utils\TextFormat;

final class ManagerLoader
{

    private static $managers = [];
    private static $loadManagers = [];

    public static function addLoadManager(string $manager){
        self::$loadManagers[] = $manager;
    }

    public static function loadManagers(): bool
    {
        self::unregisterCommands();
        $count = count(Managers::getList());
        $count++;
        $managers = Managers::getList();
        foreach ($managers as $manager) {
            $count--;
            self::$managers[$count] = new $manager;
        }
        ksort(self::$managers);
        return true;
    }

    private static function unregisterCommands()
    {
        $map = CylexCore::getInstance()->getServer()->getCommandMap();
        //TODO BETTER IMPLEMENTATION OF THIS
        foreach ($map->getCommands() as $command) {
            if ($command->getName() === "gamemode" || $command->getName() === "give" || $command->getName() === "spawn" || $command->getName() === "stop" || $command->getName() === "op" || $command->getName() === "whitelist" || $command->getName() === "deop") {
                continue;
            }
            $map->unregister($command);
        }
    }

    public static function disableManagers(): bool
    {
        foreach (self::$managers as $manager) {
            if ($manager instanceof Manager) {
                $manager->disable();
            }
        }
        return true;
    }

}