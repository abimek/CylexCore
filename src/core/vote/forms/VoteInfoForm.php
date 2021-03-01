<?php
declare(strict_types=1);

namespace core\vote\forms;

use core\forms\formapi\CustomForm;
use core\vote\cache\VotingCache;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class VoteInfoForm extends CustomForm
{

    public function __construct()
    {
        parent::__construct($this->getFormResultCallable());
        $this->setTitle(TextFormat::BOLD . TextFormat::GRAY . "Vote-Info");
        $info = VotingCache::getServerInfo();
        if (isset($info["uptime"])) {
            $this->addLabel(TextFormat::RED . "Up-Time: " . TextFormat::GRAY . $info["uptime"]);
        } else {
            $this->addLabel(TextFormat::RED . "Up-Time: " . TextFormat::GRAY . "N/A");
        }
        if (isset($info["score"])) {
            $this->addLabel(TextFormat::RED . "Score: " . TextFormat::GRAY . $info["score"]);
        } else {
            $this->addLabel(TextFormat::RED . "Score: " . TextFormat::GRAY . "N/A");
        }
        if (isset($info["rank"])) {
            $this->addLabel(TextFormat::RED . "Rank: " . TextFormat::GRAY . $info["rank"]);
        } else {
            $this->addLabel(TextFormat::RED . "Rank: " . TextFormat::GRAY . "N/A");
        }
        if (isset($info["votes"])) {
            $this->addLabel(TextFormat::RED . "Votes: " . TextFormat::GRAY . $info["votes"]);
        } else {
            $this->addLabel(TextFormat::RED . "Votes: " . TextFormat::GRAY . "N/A");
        }
        if (isset($info["favorited"])) {
            $this->addLabel(TextFormat::RED . "Favortied: " . TextFormat::GRAY . $info["favorited"]);
        } else {
            $this->addLabel(TextFormat::RED . "Favorited: " . TextFormat::GRAY . "N/A");
        }
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?array $data) {
            return;
        };
    }
}