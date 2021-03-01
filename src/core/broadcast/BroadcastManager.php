<?php
declare(strict_types=1);

namespace core\broadcast;

use core\broadcast\objects\Broadcast;
use core\broadcast\tasks\BroadcastTask;
use core\main\managers\Manager;

final class BroadcastManager extends Manager
{

    private static $broadcasts = [];

    /**
     * @param Broadcast $broadcast
     * @throws BroadcastException
     */
    public static function registerBroadcast(Broadcast $broadcast)
    {
        $id = $broadcast->getId();
        if (array_key_exists($id, self::$broadcasts)) {
            throw new BroadcastException("The broadcast object with the id ($id) already exists!");
        }
        self::$broadcasts[$id] = $broadcast;
    }

    /**
     * @return array
     */
    public static function getBroadcasts(): array
    {
        return self::$broadcasts;
    }

    protected function init(): void
    {
        $this->getCore()->getScheduler()->scheduleRepeatingTask(new BroadcastTask(), 20);
    }

    protected function close(): void
    {

    }
}