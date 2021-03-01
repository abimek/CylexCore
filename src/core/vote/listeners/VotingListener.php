<?php
declare(strict_types=1);

namespace core\vote\listeners;

use core\vote\cache\VotingCache;
use core\vote\threaded\VoteThread;
use core\vote\VoteManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\scheduler\ClosureTask;

class VotingListener implements Listener
{

    private $voteManager;

    public function __construct(VoteManager $manager)
    {
        $this->voteManager = $manager;
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        if (VotingCache::hasUnclaimedVote($player)) {
            $thread = $this->voteManager->getThread();
            $this->voteManager->getCore()->getScheduler()->scheduleDelayedTask(new ClosureTask(static function () use ($player, $thread) : void {
                if ($player->isOnline()) {
                    $thread->addActionToQueue(VoteThread::ACTION_CLAIM_VOTE, $player);
                }
            }), 20);
        }
    }
}
