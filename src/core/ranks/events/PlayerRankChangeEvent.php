<?php
declare(strict_types=1);

namespace core\ranks\events;

use core\players\objects\PlayerObject;
use core\ranks\Rank;
use pocketmine\event\player\PlayerEvent;
use pocketmine\Player;

class PlayerRankChangeEvent extends PlayerEvent
{

    private $playerObject;
    private $rank;

    public function __construct(PlayerObject $playerObject, Rank $rank)
    {
        $this->playerObject = $playerObject;
        $this->rank = $rank;
    }

    public function getPlayerObject(): PlayerObject
    {
        return $this->playerObject;
    }

    public function getRank(): Rank{
        return $this->rank;
    }

}