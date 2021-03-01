<?php
declare(strict_types=1);

namespace core\vote\events;

use pocketmine\event\Cancellable;
use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

class PlayerVoteEvent extends PlayerEvent implements Cancellable
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

    /**
     * Returns whether this instance of the event is currently cancelled.
     *
     * If it is cancelled, only downstream handlers that declare `@handleCancelled` will be called with this event.
     */
    public function isCancelled(): bool
    {
        return false;
    }
}
