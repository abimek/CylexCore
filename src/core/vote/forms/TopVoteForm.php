<?php
declare(strict_types=1);

namespace core\vote\forms;

use core\forms\formapi\CustomForm;
use core\vote\cache\VotingCache;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class TopVoteForm extends CustomForm
{

    public function __construct()
    {
        parent::__construct($this->getFormResultCallable());
        $this->setTitle(TextFormat::BOLD . TextFormat::RED . "Top-Voters");
        $info = VotingCache::getTopVoters();
        if (count($info) < 1) {
            $this->addLabel(TextFormat::GRAY . "There are currently no voters!");
            return;
        }
        foreach ($info as $player => $votes) {
            $this->addLabel(TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . $player . TextFormat::GRAY . "]" . TextFormat::RED . "votes: " . TextFormat::GRAY . "[" . TextFormat::RED . $votes . TextFormat::GRAY . "]");
        }
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?array $data) {
            return;
        };
    }
}