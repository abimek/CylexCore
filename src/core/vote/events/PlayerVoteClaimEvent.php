<?php
declare(strict_types=1);

namespace core\vote\events;

use pocketmine\event\Cancellable;
use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

class PlayerVoteClaimEvent extends PlayerEvent implements Cancellable
{

    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function isCancelled(): bool
    {
        return false;
    }
}
