<?php
declare(strict_types=1);

namespace core;

use core\main\managers\ManagerLoader;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\plugin\PluginBase;
use xenialdan\apibossbar\PacketListener;

class CylexCore extends PluginBase
{

    /**
     * @var CylexCore
     */
    private static $instance;

    /**
     * @return CylexCore
     */
    public static function getInstance(): CylexCore
    {
        return self::$instance;
    }

    public function onDisable()
    {
        ManagerLoader::disableManagers();
    }

    public function onEnable()
    {
        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }
        if (!PacketListener::isRegistered()) {
            PacketListener::register($this);
        }
        self::$instance = $this;
        ManagerLoader::loadManagers();
    }
}
