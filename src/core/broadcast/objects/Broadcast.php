<?php
declare(strict_types=1);

namespace core\broadcast\objects;

use core\players\PlayerManager;
use core\players\session\PlayerSession;
use core\ranks\RankManager;
use core\ranks\types\RankTypes;
use pocketmine\Server;

class Broadcast
{

    private $tick = 0;

    private $identifier;
    private $text;
    private $repeat_period;

    public function __construct(string $identifier, string $text, int $repeat_period)
    {
        $this->identifier = $identifier;
        $this->text = $text;
        $this->repeat_period = $repeat_period;
    }

    public function getId(): string
    {
        return $this->identifier;
    }

    public function broadcastToStaff(): void
    {
        foreach (PlayerManager::getSessions() as $session) {
            if ($session instanceof PlayerSession) {
                $rank_id = $session->getRankIdentifier();
                if (RankManager::getRank($rank_id)->getType() === RankTypes::STAFF_RANK) {
                    $session->getPlayer()->sendMessage($this->getText());
                }
            }
        }
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function tick()
    {
        $this->tick++;
        if ($this->tick === $this->getRepeatPeriod()) {
            $this->tick = 0;
            Server::getInstance()->broadcastMessage($this->getText());
        }
    }

    public function getRepeatPeriod(): int
    {
        return $this->repeat_period;
    }
}