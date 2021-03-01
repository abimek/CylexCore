<?php
declare(strict_types=1);

namespace core\admin\handlers;

use core\admin\database\ReportDatabaseHandler;
use core\admin\objects\Report;
use pocketmine\uuid\UUID;

class ReportHandler
{

    public static function createReport(string $reporter, string $reason, string $reported_person)
    {
        $report = new Report(UUID::fromRandom()->toString(), $reported_person, $reason, $reported_person, time());
        ReportDatabaseHandler::addReport($report, false);
    }

    public static function deleteReport(string $id)
    {
        ReportDatabaseHandler::deleteReport($id);
    }
}