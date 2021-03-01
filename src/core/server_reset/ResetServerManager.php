<?php
declare(strict_types=1);

namespace core\server_reset;

use core\CylexCore;
use core\server_reset\tasks\ResetServerTask;

class ResetServerManager
{
    public const TIME = 20 * (60 * 60);

    public static function enableReset(int $time = self::TIME)
    {
        CylexCore::getInstance()->getScheduler()->scheduleDelayedTask(new ResetServerTask(), $time);
    }
}