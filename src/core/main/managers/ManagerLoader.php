<?php
declare(strict_types=1);

namespace core\main\managers;

use core\CylexCore;
use Exception;
use pocketmine\utils\TextFormat;

final class ManagerLoader
{

    private static $managers = [];

    public static function loadManagers(): bool
    {
        self::unregisterCommands();
        $count = count(Managers::getList());
        $count++;
        $managers = Managers::getList();
        foreach ($managers as $manager) {
            try {
                $count--;
                self::$managers[$count] = new $manager;
            } catch (Exception $exception) {
                var_dump(TextFormat::GOLD . "issue in file ==> $manager");
                var_dump(TextFormat::RED . "An error has just occured ===> " . $exception);
                return false;
            }
        }
        ksort(self::$managers);
        return true;
    }

    private static function unregisterCommands()
    {
        $map = CylexCore::getInstance()->getServer()->getCommandMap();
        foreach ($map->getCommands() as $command) {
            if ($command->getName() === "gamemode" || $command->getName() === "give" || $command->getName() === "spawn" || $command->getName() === "stop" || $command->getName() === "op" || $command->getName() === "whitelist" || $command->getName() === "deop" || $command->getName() === "timings") {
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