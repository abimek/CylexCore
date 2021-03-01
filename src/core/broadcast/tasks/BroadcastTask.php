<?php
declare(strict_types=1);

namespace core\broadcast\tasks;

use core\broadcast\BroadcastManager;
use core\broadcast\objects\Broadcast;
use pocketmine\scheduler\Task;

class BroadcastTask extends Task
{

    /**
     * Actions to execute when run
     */
    public function onRun(): void
    {
        $broadcasts = BroadcastManager::getBroadcasts();
        foreach ($broadcasts as $broadcast) {
            if ($broadcast instanceof Broadcast) {
                $broadcast->tick();
            }
        }
    }
}