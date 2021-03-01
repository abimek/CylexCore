<?php
declare(strict_types=1);

namespace core\admin\forms\reports;

use core\admin\database\ReportDatabaseHandler;
use core\admin\objects\Report;
use core\forms\formapi\SimpleForm;
use core\main\text\message\Message;
use core\main\text\TextFormat;
use pocketmine\player\Player;

class ReportsForm extends SimpleForm
{

    private $ids = [];

    public function __construct(Player $player)
    {
        parent::__construct($this->getFormResultCallable());
        $this->setTitle(TextFormat::BOLD_LIGHT_PURPLE . "Reports-List");
        foreach (ReportDatabaseHandler::getReports() as $report) {
            if ($report instanceof Report) {
                $this->ids[] = $report->getId();
                $this->addButton($this->getReportFormat($report));
            }
        }
        if (empty($this->ids)) {
            $this->setContent(Message::ARROW . "It seems we currently do not have any reports!");
        }
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?int $data = null) {
            $result = $data;
            if ($result === null) {
                $player->sendMessage(Message::PREFIX . "You forgot to select a report!");
                return;
            }
            $id = $this->ids[$result];
            $report = ReportDatabaseHandler::getReport($id);
            if ($report->isBeingHandled()) {
                $player->sendMessage(Message::PREFIX . "Someone is already dealing with this report, please select a different one!");
                $form = new ReportsForm($player);
                $player->sendForm($form);
                return;
            }
            $form = new ReportInfoForm($player, $report);
            $player->sendForm($form);
            $report->setBeingHandled(true);

        };
    }

    private function getReportFormat(Report $report): string
    {
        return Message::ARROW . TextFormat::GREEN . $report->getReporter() . TextFormat::LIGHT_PURPLE . " | " . TextFormat::RED . $report->getReportedPerson();
    }
}