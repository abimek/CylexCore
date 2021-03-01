<?php
declare(strict_types=1);

namespace core\admin\forms\reports;

use core\admin\handlers\ReportHandler;
use core\forms\formapi\CustomForm;
use core\main\text\message\Message;
use core\players\PlayerManager;
use core\ranks\levels\StaffRankLevels;
use core\ranks\RankManager;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class ReportForm extends CustomForm
{
    private $reported_person;

    public function __construct(Player $player, $reported_person)
    {
        $this->reported_person = $reported_person;
        $session = PlayerManager::getSession($player->getXuid());
        if ($session === null) {
            return;
        }
        $rank_id = $session->getRankIdentifier();
        $rank = RankManager::getRank($rank_id);
        if ($rank === null) {
            return;
        }
        parent::__construct($this->getFormResultCallable());
        if ($rank->getLevel() >= StaffRankLevels::MOD) {
            $this->setTitle(TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Report-Form");
            $this->addInput(Message::ARROW . TextFormat::RED . "Reason", "", "");
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
            ReportHandler::createReport($player->getName(), $data[0], $this->reported_person);
            $player->sendMessage(Message::PREFIX . "Successfully reported the player " . TextFormat::RED . $this->reported_person);
            return;
        };
    }
}


