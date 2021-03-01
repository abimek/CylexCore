<?php
declare(strict_types=1);

namespace core\admin\forms\reports;

use core\admin\database\ReportDatabaseHandler;
use core\admin\objects\Report;
use core\forms\formapi\CustomForm;
use core\main\text\message\Message;
use core\main\text\utils\TextTimeUtil;
use core\players\PlayerManager;
use core\ranks\levels\StaffRankLevels;
use core\ranks\RankManager;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class ReportInfoForm extends CustomForm
{
    private $report;

    public function __construct(Player $player, Report $report)
    {
        $this->report = $report;
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
            $this->setTitle(TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Report-Info");
            $this->addLabel(Message::ARROW . "Reporter: " . TextFormat::RED . $report->getReporter());
            $this->addLabel(Message::ARROW . "Reported-Person: " . TextFormat::RED . $report->getReportedPerson());
            $this->addLabel(Message::ARROW . "Reason: " . TextFormat::LIGHT_PURPLE . $report->getReason());
            $this->addLabel(Message::ARROW . "Time: " . TextTimeUtil::secondsToTime(time() - $report->getTime(), TextFormat::LIGHT_PURPLE, TextFormat::RED));
            $this->addToggle(Message::ARROW . "Delete", true);
        }
    }


    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?array $data = null) {
            if ($data === null) {
                return;
            }
            if ($data[4] === true) {
                ReportDatabaseHandler::deleteReport($this->report->getId());
                $player->sendMessage(Message::PREFIX . "Report successfully deleted!");
                return;
            }
            $this->report->setBeingHandled(false);
            return;
        };
    }
}


