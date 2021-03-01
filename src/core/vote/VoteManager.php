<?php
declare(strict_types=1);

namespace core\vote;

use core\main\managers\Manager;
use core\network\Links;
use core\vote\cache\VotingCache;
use core\vote\commands\VoteCommand;
use core\vote\listeners\VotingListener;
use core\vote\threaded\VoteThread;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;

class VoteManager extends Manager
{

    private static $instance;
    /**
     * @var VoteThread
     */
    private $thread;

    public static function getInstance(): VoteManager
    {
        return self::$instance;
    }

    public function getThread(): VoteThread
    {
        return $this->thread;
    }

    protected function init(): void
    {
        self::$instance = $this;
        $this->registerTasks();
        $this->tryRegisteringCommand();
    }

    private function registerTasks()
    {
        $this->thread = $thread = new VoteThread();
        $this->thread->run();
        Server::getInstance()->getPluginManager()->registerEvents(new VotingListener($this), $this->getCore());
        $this->registerUpdatesTask();
        $server = $this->getCore()->getServer();
        $this->getCore()->getScheduler()->scheduleRepeatingTask(new ClosureTask(static function () use ($thread, $server) : void {
            $thread->collectActionResults($server);
        }), 1);
    }

    private function registerUpdatesTask()
    {
        $thread = $this->thread;
        $thread->setApiKey(Links::VOTE_API_KEY);
        $this->getCore()->getScheduler()->scheduleRepeatingTask(new ClosureTask(static function () use ($thread) : void {
            $thread->addActionToQueue(VoteThread::ACTION_UPDATE_CACHE);

        }), VotingCache::TIME_BETWEEN_CACHE_UPDATE * 20);
    }

    public function tryRegisteringCommand()
    {
        Server::getInstance()->getCommandMap()->register("vote", new VoteCommand());
    }

    protected function close(): void
    {
        // TODO: Implement close() method.
    }
}