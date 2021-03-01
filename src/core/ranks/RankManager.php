<?php
declare(strict_types=1);

namespace core\ranks;

use core\main\managers\Manager;
use core\ranks\commands\SetRankCommand;
use core\ranks\listeners\ChatFormatListener;
use core\ranks\ranks\Admin;
use core\ranks\ranks\Builder;
use core\ranks\ranks\Developer;
use core\ranks\ranks\Helper;
use core\ranks\ranks\Mod;
use core\ranks\ranks\Owner;
use core\ranks\ranks\Rookie;
use core\ranks\tasks\DisplayTagUpdateTask;
use pocketmine\Server;

final class RankManager extends Manager
{

    /**
     * @var array
     */
    private static $editCallables = [];

    private static $displayTagCallables = [];
    private static $ranks;

    /**
     * @param callable $callable
     */
    public static function registerEditCallable(callable $callable)
    {
        self::$editCallables[] = $callable;
    }

    /**
     * @param callable $callable
     */
    public static function registerDisplayTagCallabe(callable $callable)
    {
        self::$displayTagCallables[] = $callable;
    }

    /**
     * @return array
     */
    public static function getEditCallables(): array
    {
        return self::$editCallables;
    }

    /**
     * @return array
     */
    public static function getDisplayTagCallabes(): array
    {
        return self::$displayTagCallables;
    }

    /**
     * @param string $id
     * @return Rank|null
     */
    public static function getRank(string $id): ?Rank
    {
        $rank = self::$ranks[$id] ?? null;
        return $rank;
    }

    protected function init(): void
    {
        self::registerListener(new ChatFormatListener());
        self::registerRank(new Owner());
        self::registerRank(new Developer());
        self::registerRank(new ranks\Manager());
        self::registerRank(new Admin());
        self::registerRank(new Mod());
        self::registerRank(new Helper());
        self::registerRank(new Builder());
        self::registerRank(new Rookie());
        $this->getCore()->getScheduler()->scheduleRepeatingTask(new DisplayTagUpdateTask(), 20);
        Server::getInstance()->getCommandMap()->register("setrank", new SetRankCommand());
    }

    /**
     * @param Rank $rank
     * @return Rank
     * @throws RankException
     */
    public static function registerRank(Rank $rank): Rank
    {
        $id = $rank->getIdentifier();
        if (isset(self::$ranks[$id])) {
            throw new RankException("Tried registering an already register rank with the identifier ($id)!");
        }
        self::$ranks[$id] = $rank;
        return $rank;
    }

    protected function close(): void
    {

    }
}