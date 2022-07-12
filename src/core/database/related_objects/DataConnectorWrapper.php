<?php
declare(strict_types=1);

namespace core\database\related_objects;

use poggit\libasynql\DataConnector;
use poggit\libasynql\result\SqlChangeResult;
use poggit\libasynql\result\SqlInsertResult;
use poggit\libasynql\result\SqlSelectResult;
use poggit\libasynql\SqlThread;

final class DataConnectorWrapper {

    /**
     * @var DataConnector
     */
    private $database;

    public function __construct(DataConnector $connector)
    {
        $this->database = $connector;
    }

    public function getDatabase(): DataConnector{
        return $this->database;
    }

    public function executeChangeRaw(string $query, array $args = [], ?callable $onSuccess = null, ?callable $onError = null) : void{
        $this->executeImplLast($query, $args, SqlThread::MODE_CHANGE, static function(SqlChangeResult $result) use ($onSuccess){
            if($onSuccess !== null){
                $onSuccess($result->getAffectedRows());
            }
        }, $onError);
    }

    public function executeInsertRaw(string $query, array $args = [], ?callable $onInserted = null, ?callable $onError = null) : void{
        $this->executeImplLast($query, $args, SqlThread::MODE_INSERT, static function(SqlInsertResult $result) use ($onInserted){
            if($onInserted !== null){
                $onInserted($result->getInsertId(), $result->getAffectedRows());
            }
        }, $onError);
    }

    public function executeSelectRaw(string $query, array $args = [], ?callable $onSelect = null, ?callable $onError = null) : void{
        $this->executeImplLast($query, $args, SqlThread::MODE_SELECT, static function(SqlSelectResult $result) use ($onSelect){
            if($onSelect !== null){
                $onSelect($result->getRows(), $result->getColumnInfo());
            }
        }, $onError);
    }

    public function executeGenericRaw(string $query, array $args = [], ?callable $onSuccess = null, ?callable $onError = null) : void{
        $this->executeImplLast($query, $args, SqlThread::MODE_GENERIC, static function() use ($onSuccess){
            if($onSuccess !== null){
                $onSuccess();
            }
        }, $onError);
    }



    private function executeImplLast(string $query, array $args, int $mode, callable $handler, ?callable $onError) : void{
        $this->executeImpl($query, $args, $mode, static function($results) use($handler){
            $handler($results[count($results) - 1]);
        }, $onError);
    }


    private function executeImpl(string $query, array $args, int $mode, callable $handler, ?callable $onError) : void{
        $this->database->executeImplRaw([$query], [$args], [$mode], $handler, $onError);
    }

}
