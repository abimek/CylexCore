<?php
declare(strict_types=1);

namespace core\players\session;

use core\main\text\TextFormat;
use core\players\objects\PlayerObject;
use core\ranks\ranks\Rookie;
use pocketmine\player\Player;

class PlayerSession
{

    private static $ranks = [];
    private $object;
    private $player;
    private $rank;
    private $wants_gui = false;

    /**
     * @var null|string
     */
    private $partTime_rank;

    public function __construct(Player $player, PlayerObject $object, $wants_gui = false)
    {
        $this->player = $player;
        $this->object = $object;
        $this->wants_gui = $wants_gui;
        $this->rank = $object->getRank();
        $this->partTime_rank = null;
        if (isset(self::$ranks[$this->rank])) {
            $player->setNameTag(self::$ranks[$this->rank] . TextFormat::RESET_GRAY . " " . $player->getName());
        }
    }

    public static function registerRankTag(string $rankIdentifier, string $format)
    {
        self::$ranks[$rankIdentifier] = $format;
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * @return PlayerObject
     */
    public function getObject(): PlayerObject
    {
        return $this->object;
    }

    /**
     * @return string
     */
    public function getRankIdentifier(): string
    {
        if ($this->partTime_rank === null) {
            return $this->rank;
        } else {
            return $this->partTime_rank;
        }
    }

    /**
     * @return bool
     */
    public function isStaff(): bool
    {
        if ($this->rank === Rookie::ROOKIE) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param string $rank
     */
    public function setRank(string $rank): void
    {
        $this->rank = $rank;
    }

    /**
     * @return bool
     */
    public function wantsGUI(): bool
    {
        return $this->wants_gui;
    }

    /**
     * @param string $rank
     */
    public function setPartTimeRank(string $rank): void
    {
        $this->partTime_rank = $rank;
    }

}
