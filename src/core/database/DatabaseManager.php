<?php
declare(strict_types=1);

namespace core\database;

use core\CylexCore;
use core\database\threads\SQLThread;
use core\database\related_objects\DataConnectorWrapper;
use poggit\libasynql\libasynql;
use poggit\libasynql\SqlError;

final class DatabaseManager
{

    private static $queries = [];

    /**
     * @var DataConnectorWrapper
     */
    public $database;

    /**
     * @var DataConnectorWrapper
     */
    public $database2;

    private static $sqlThread;

    private static $instance;


    public function __construct()
    {
        $this->init();
    }

    protected function init(): void
    {
        self::$instance = $this;
        $this->database = new DataConnectorWrapper(libasynql::create(CylexCore::getInstance(), CylexCore::getInstance()->getConfig()->get("database"), [
            "mysql" => "mysql.sql"
        ]));
        $this->database2 = new DataConnectorWrapper(libasynql::create(CylexCore::getInstance(), CylexCore::getInstance()->getConfig()->get("database2"), [
           "mysql" => "mysql.sql"
        ]));
        //self::$sqlThread = new SQLThread([$host, $password, $username, $dbname]);
       // CylexCore::getInstance()->getScheduler()->scheduleRepeatingTask(new SQLCollectionTask(self::$sqlThread), 1);
    }

    public static function getInstance(): DatabaseManager{
        return self::$instance;
    }

    public static function query(string $query, int $dbType = 0, ?array $parameters = [], ?callable $callable = null){
        if ($parameters === null){
            $parameters = [];
        }
        $type = strtok($query, " ");
        switch ($type){
            case "INSERT":
                if (!is_array($parameters)){
                    throw new \Exception("Unable to query, $parameters is lot an array on insert!");
                }
                if ($dbType === 0){
                    self::getInstance()->database->executeInsertRaw($query, $parameters, $callable, function (SqlError $error, \Exception $trace){
                        var_dump($error);
                    });
                }else{
                    self::getInstance()->database2->executeInsertRaw($query, $parameters, $callable, function (SqlError $error, \Exception $trace){
                        var_dump($error);
                    });
                }
                return;
            case "SELECT":
                if (!is_array($parameters)){
                    throw new \Exception("Unable to query, $parameters is lot an array on select!");
                }
                if ($dbType === 0){
                    self::getInstance()->database->executeSelectRaw($query, $parameters, $callable, function (SqlError $error, \Exception $trace){
                        var_dump($error);
                    });
                }else{
                    self::getInstance()->database2->executeSelectRaw($query, $parameters, $callable, function (SqlError $error, \Exception $trace){
                        var_dump($error);
                    });
                }
                return;
            case "CREATE":
                if (!is_array($parameters)){
                    throw new \Exception("Unable to query, $parameters is lot an array on create!");
                }
                if ($dbType === 0){
                    self::getInstance()->database->executeGenericRaw($query, $parameters, $callable, function (SqlError $error, \Exception $trace){
                        var_dump($error);
                    });
                }else{
                    self::getInstance()->database2->executeGenericRaw($query, $parameters, $callable, function (SqlError $error, \Exception $trace){
                        var_dump($error);
                    });
                }
                return;
            case "DELETE":
                if (!is_array($parameters)){
                    throw new \Exception("Unable to query, $parameters is lot an array on delete!");
                }
                if ($dbType === 0){
                    self::getInstance()->database->executeGenericRaw($query, $parameters, $callable, function (SqlError $error, \Exception $trace){
                        var_dump($error);
                    });
                }else{
                    self::getInstance()->database2->executeGenericRaw($query, $parameters, $callable, function (SqlError $error, \Exception $trace){
                        var_dump($error);
                    });
                }
                return;
            case "UPDATE":
                if (!is_array($parameters)){
                    throw new \Exception("Unable to query, $parameters is lot an array on update!");
                }
                if ($dbType === 0){
                    self::getInstance()->database->executeChangeRaw($query, $parameters, $callable, function (SqlError $error, \Exception $trace){
                        var_dump($error);
                    });
                }else{
                    self::getInstance()->database2->executeChangeRaw($query, $parameters, $callable, function (SqlError $error, \Exception $trace){
                        var_dump($error);
                    });
                }
                return;
            case "DROP":
                if (!is_array($parameters)){
                    throw new \Exception("Unable to query, $parameters is lot an array on drop!");
                }
                if ($dbType === 0){
                    self::getInstance()->database->executeGenericRaw($query, $parameters, $callable, function (SqlError $error, \Exception $trace){
                        var_dump($error);
                    });
                }else{
                    self::getInstance()->database2->executeGenericRaw($query, $parameters, $callable, function (SqlError $error, \Exception $trace){
                        var_dump($error);
                    });
                }
                return;
        }
        var_dump("uncomplete query");
        var_dump($type);
        var_dump($query);
    }

    public static function getThread(): SQLThread
    {
        return self::$sqlThread;
    }

    public static function emptyQuery(string $statement, $dbType = 0, ?array $parameters = [])
    {
        self::query($statement, $dbType, $parameters);
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
        //self::$sqlThread->close();
    }

    public static function realClose(): void {
        if (isset(self::getInstance()->database)) self::getInstance()->database->close();
        if (isset(self::getInstance()->database2)) self::getInstance()->database2->close();
    }
}