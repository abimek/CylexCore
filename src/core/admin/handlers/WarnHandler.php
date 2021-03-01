<?php
declare(strict_types=1);

namespace core\admin\handlers;

use core\main\text\TextFormat;
use core\players\session\PlayerSession;

class WarnHandler
{

    public static function warnPlayer(PlayerSession $session, string $reason)
    {
        $session->getPlayer()->sendTitle(TextFormat::BOLD_RED . "Warning!", $reason);
    }
}