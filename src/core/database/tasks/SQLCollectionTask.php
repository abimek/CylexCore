<?php
declare(strict_types=1);

namespace core\database\tasks;

use core\database\DatabaseManager;
use core\database\objects\Query;
use core\database\threads\SQLThread;
use pocketmine\scheduler\Task;

final class SQLCollectionTask extends Task
{

    /**
     * @var SQLThread
     */
    private $thread;

    public function __construct(SQLThread $thread)
    {
        $this->thread = $thread;
    }

    /**
     * Actions to execute when run
     */
    public function onRun(int $currentTick): void
    {
        $output_threaded = $this->thread->getOutput();
        while (($info = $output_threaded->shift()) !== null) {
            $info = unserialize($info);
            if ($info === "cool:bool") {
                var_dump("kills");
                $this->thread->kill();
            }
            $key = $info["key"];
            $data = $info["data"];
            if (isset(DatabaseManager::getQueries()[$key])) {
                $query = DatabaseManager::getQueries()[$key];
                if ($query != null && $query instanceof Query) {
                    $callable = $query->getCallable();
                    $callable($data);
                }
            }
        }
    }

}