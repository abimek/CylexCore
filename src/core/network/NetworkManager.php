<?php
declare(strict_types=1);

namespace core\network;

use core\main\managers\Manager;
use core\network\commands\MailCommand;
use core\network\commands\ProfileCommand;
use core\network\commands\SettingsCommand;
use core\network\database\NetworkDatabaseHandler;
use core\network\listener\NetworkPlayerListener;
use pocketmine\Server;

class NetworkManager extends Manager
{

    /**
     * @var NetworkDatabaseHandler
     */
    private static $networkDB;

    public static function getNetworkPlayerDBHandler(): NetworkDatabaseHandler
    {
        return self::$networkDB;
    }

    protected function init(): void
    {
        self::$networkDB = new NetworkDatabaseHandler();
        $this->registerListener(new NetworkPlayerListener());
        Server::getInstance()->getCommandMap()->register("settings", new SettingsCommand());
        Server::getInstance()->getCommandMap()->register("mail", new MailCommand());
        Server::getInstance()->getCommandMap()->register("profile", new ProfileCommand());
    }

    protected function close(): void
    {
        self::$networkDB->close();
    }
}