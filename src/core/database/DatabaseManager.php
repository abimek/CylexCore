<?php
declare(strict_types=1);

namespace core\database;

use core\CylexCore;
use core\database\objects\Query;
use core\database\tasks\SQLCollectionTask;
use core\database\threads\SQLThread;
use core\HomesLoader;
use core\Loader;

final class DatabaseManager
{

    private static $queries = [];
    /**
     * @var SQLThread
     */
    private static $sqlThread;

    private static $instance;

    public function __construct()
    {
        $this->init();
    }

    protected function init(): void
    {
        $data = CylexCore::getInstance()->getConfig();
        $host = $data->get("Host");
        $password = $data->get("Password");
        $username = $data->get("Username");
        $dbname = $data->get("DbName");
        self::$instance = $this;
        self::$sqlThread = new SQLThread([$host, $password, $username, $dbname]);
        CylexCore::getInstance()->getScheduler()->scheduleRepeatingTask(new SQLCollectionTask(self::$sqlThread), 1);
    }

    /**
     * @param Query $query
     */
    public static function query(Query $query)
    {
        self::$queries[$query->getKey()] = $query;
        self::getThread()->query($query);
    }

    public static function getThread(): SQLThread
    {
        return self::$sqlThread;
    }

    public static function emptyQuery(string $statement, $dbType = 0, ?array $parameters = null)
    {
        self::getThread()->emptyQuery($statement, $parameters, $dbType);
    }

    /**
     * @param string $identifier
     */
    public static function removeQuery(string $identifier)
    {
        if (isset(self::$queries[$identifier])) {
            unset(self::$queries[$identifier]);
        }
    }

    /**
     * @return array
     */
    public static function getQueries(): array
    {
        return self::$queries;
    }

    public static function close(): void
    {
        self::$sqlThread->close();
    }
}