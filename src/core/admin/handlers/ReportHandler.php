<?php
declare(strict_types=1);

namespace core\admin\handlers;

use core\admin\database\ReportDatabaseHandler;
use core\admin\objects\Report;
use Ramsey\Uuid\Uuid;

class ReportHandler
{

    public static function createReport(string $reporter, string $reason, string $reported_person)
    {
        $report = new Report(Uuid::uuid4()->toString(), $reporter, $reason, $reported_person, time());
        ReportDatabaseHandler::addReport($report, false);
    }

    public static function deleteReport(string $id)
    {
        ReportDatabaseHandler::deleteReport($id);
    }
}