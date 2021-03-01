<?php
declare(strict_types=1);

namespace core\server_reset\tasks;

use core\main\text\TextFormat;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class ResetServerTask extends Task
{

    /**
     * Actions to execute when run
     */
    public function onRun(): void
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            $player->kick(TextFormat::RED . "Server has reset, " . TextFormat::GOLD . "join now!");
        }
        Server::getInstance()->shutdown();
    }
}