<?php
declare(strict_types=1);

namespace core\admin\forms\mute;

use core\admin\handlers\MuteHandler;
use core\forms\formapi\CustomForm;
use core\main\text\message\Message;
use core\players\objects\PlayerObject;
use core\players\PlayerManager;
use core\ranks\levels\StaffRankLevels;
use core\ranks\RankManager;
use core\ranks\types\RankTypes;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class MuteForm extends CustomForm
{
    private $person;

    public function __construct(Player $player, PlayerObject $person)
    {
        $this->person = $person;
        $session = PlayerManager::getSession($player->getXuid());
        if ($session === null) {
            return;
        }
        $rank_id = $session->getRankIdentifier();
        $rank = RankManager::getRank($rank_id);
        if ($rank === null || $rank->getType() === RankTypes::NORMAL_RANK) {
            return;
        }
        parent::__construct($this->getFormResultCallable());
        if ($rank->getLevel() >= StaffRankLevels::HELPER) {
            $this->setTitle(TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Mute-Form");
            $this->addInput(Message::ARROW . TextFormat::RED . "Reason", "", "");
            $this->addSlider(Message::ARROW . "Minutes: ", 0, 60, 1, 0);
            $this->addSlider(Message::ARROW . "Hour: ", 0, 23, 1, 0);
        }
    }


    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?array $data = null) {
            if ($data === null) {
                return;
            }
            if ($data[0] === "") {
                $player->sendMessage(Message::PREFIX . "you inserted invalid parameters and were-not able to do a successful ban, please try again!");
                return;
            }
            $reason = $data[0];
            $min = $data[1] * 60;
            $hour = $data[2] * 60 * 60;
            if ($min === 0 && $hour === 0) {
                return;
            }
            $amount = $min + $hour;
            MuteHandler::mutePlayer($player->getName(), $reason, $this->person, (int)$amount);
            return;
        };
    }
}
