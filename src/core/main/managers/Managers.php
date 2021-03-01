<?php
declare(strict_types=1);

namespace core\main\managers;

use core\admin\AdminManager;
use core\broadcast\BroadcastManager;
use core\database\DatabaseManager;
use core\forms\FormManager;
use core\network\NetworkManager;
use core\players\PlayerManager;
use core\ranks\RankManager;
use core\vote\VoteManager;

final class Managers
{

    public static function getList(): array
    {
        //classes should go in here
        return [
            DatabaseManager::class,
            RankManager::class,
            PlayerManager::class,
            NetworkManager::class,
            FormManager::class,
            BroadcastManager::class,
            AdminManager::class,
            VoteManager::class
        ];
    }

}