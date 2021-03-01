<?php
declare(strict_types=1);

namespace core\ranks\events;

use core\players\objects\PlayerObject;
use pocketmine\event\player\PlayerEvent;

class PlayerRankChangeEvent extends PlayerEvent
{

    private $playerObject;

    public function __construct(PlayerObject $playerObject)
    {
        $this->playerObject = $playerObject;

    }

    public function getPlayerObject(): PlayerObject
    {
        return $this->playerObject;
    }

}