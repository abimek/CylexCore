<?php
declare(strict_types=1);

namespace core\network\forms;

use core\forms\formapi\CustomForm;
use core\main\text\TextFormat;
use core\network\objects\NetworkPlayer;
use pocketmine\player\Player;

class ProfileForm extends CustomForm
{

    public function __construct(Player $player, NetworkPlayer $player2)
    {
        parent::__construct($this->getFormResultCallable());
        $this->setTitle(TextFormat::BOLD_GRAY . $player2->getUsername());
        if ($player2->getYoutube() !== "") {
            $this->addLabel(TextFormat::RED . "Youtube: " . TextFormat::GRAY . $player2->getYoutube());
        }
        if ($player2->getDiscord() !== "") {
            $this->addLabel(TextFormat::RED . "Discord: " . TextFormat::GRAY . $player2->getDiscord());
        }
        if ($player2->getDescription() !== "") {
            $this->addLabel(TextFormat::RED . "Description: " . TextFormat::GRAY . $player2->getDescription());
        }
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?array $data) {
            return;
        };
    }
}