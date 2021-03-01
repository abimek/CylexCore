<?php
declare(strict_types=1);

namespace core\players;

use core\main\managers\Manager;
use core\players\database\PlayerDatabaseHandler;
use core\players\listener\PlayerListener;
use core\players\objects\PlayerObject;
use core\players\session\PlayerSession;
use pocketmine\player\Player;

final class PlayerManager extends Manager
{

    private static $creation_callables = [];
    private static $distruction_callables = [];
    private static $sessions = [];
    private static $nameMap = [];

    /**
     * @var PlayerDatabaseHandler
     */
    private static $databaseHandler;

    /**
     * @param Player $player
     * @param PlayerObject $object
     */
    public static function createSession(Player $player, PlayerObject $object, bool $wantsgui)
    {
        $xuid = $player->getXuid();
        if (isset(self::$sessions[$xuid])) {
            return;
        }
        $session = new PlayerSession($player, $object, $wantsgui);
        foreach (self::$creation_callables as $callable) {
            $callable($session);
        }
        self::$sessions[$xuid] = $session;
        self::$nameMap[$player->getName()] = $xuid;
    }

    public static function deleteSession(Player $player)
    {
        $xuid = $player->getXuid();
        if (isset(self::$sessions[$xuid])) {
            $session = self::$sessions[$xuid];
            if ($session instanceof PlayerSession) {
                self::getDatabaseHandler()->savePlayer($session->getObject());
            }
            foreach (self::$distruction_callables as $distruction_callable) {
                $distruction_callable($session);
            }
            unset(self::$sessions[$xuid]);
            unset(self::$nameMap[$player->getName()]);
        }
    }

    /**
     * @return PlayerDatabaseHandler
     */
    public static function getDatabaseHandler(): PlayerDatabaseHandler
    {
        return self::$databaseHandler;
    }

    /**
     * @param string $xuid
     * @return PlayerSession|null
     */
    public static function getSession(string $xuid): ?PlayerSession
    {
        if (!isset(self::$sessions[$xuid])) {
            return null;
        }
        return self::$sessions[$xuid];
    }

    public static function getSessions(): array
    {
        return self::$sessions;
    }

    /**
     * @param string $name
     * @return PlayerSession|null
     */
    public static function getSessionByUsername(string $name): ?PlayerSession
    {
        if (!isset(self::$nameMap[$name])) {
            return null;
        }
        return self::$sessions[self::$nameMap[$name]];
    }

    public static function addPlayerSessionCreationCallable($callable)
    {
        self::$creation_callables[] = $callable;
    }

    public static function addPlayerSessionDestructionCallable($callable)
    {
        self::$distruction_callables[] = $callable;
    }

    protected function init(): void
    {
        self::$databaseHandler = new PlayerDatabaseHandler();
        $this->registerListener(new PlayerListener());
    }

    protected function close(): void
    {
        self::$databaseHandler->close();
    }
}