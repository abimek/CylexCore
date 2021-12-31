<?php
declare(strict_types=1);

namespace core\admin\database;

use core\admin\objects\Report;
use core\database\DatabaseManager;
use core\database\objects\Query;

class ReportDatabaseHandler
{

    private static $reports = [];

    public function __construct()
    {
        $this->init();
    }

    private function init()
    {
        DatabaseManager::query("CREATE TABLE IF NOT EXISTS reports(id VARCHAR(36) PRIMARY KEY, reporter TEXT, reason TEXT, reported_person TEXT, unix_time INTEGER);", 0, [], function ($result) {
            DatabaseManager::query("SELECT * FROM reports", 0, [], function ($result) {
                foreach ($result as $row) {
                    $report = new Report($row["id"], $row["reporter"], $row["reason"], $row["reported_person"], $row["unix_time"]);
                    self::$reports[$row["id"]] = $report;
                }
            });
        });
    }

    /**
     * @param string $id
     */
    public static function deleteReport(string $id)
    {
        if (isset(self::$reports[$id])) {
            unset(self::$reports[$id]);
            DatabaseManager::emptyQuery("DELETE FROM reports WHERE id=?", 0, [$id]);
        }
    }

    /**
     * @param string $id
     * @return Report|null
     */
    public static function getReport(string $id): ?Report
    {
        if (isset(self::$reports[$id])) {
            return self::$reports[$id];
        }
        return null;
    }

    public static function getReports(): array
    {
        return self::$reports;
    }

    /**
     * @param Report $report
     * @param bool $update
     * @return bool
     */
    public static function addReport(Report $report, bool $update = false): bool
    {
        if (isset(self::$reports[$report->getId()]) && $update === false) {
            return false;
        }
        self::$reports[$report->getId()] = $report;
        if ($update) {
            DatabaseManager::emptyQuery("INSERT IGNORE INTO reports(id, reporter, reason, reported_person, unix_time) VALUES (?, ?, ?, ?, ?);", Query::SERVER_DB, [
                $report->getId(),
                $report->getReporter(),
                $report->getReason(),
                $report->getReportedPerson(),
                $report->getTime()
            ]);
            DatabaseManager::emptyQuery("UPDATE reports SET id=?, reporter=?, reason=?, reported_person=?, unix_time=? WHERE id=?", Query::SERVER_DB, [
                $report->getId(),
                $report->getReporter(),
                $report->getReason(),
                $report->getReportedPerson(),
                $report->getTime(),
                $report->getId()
            ]);
        } else {
            DatabaseManager::emptyQuery("INSERT IGNORE INTO reports(id, reporter, reason, reported_person, unix_time) VALUES (?, ?, ?, ?, ?);", Query::SERVER_DB, [
                $report->getId(),
                $report->getReporter(),
                $report->getReason(),
                $report->getReportedPerson(),
                $report->getTime()
            ]);
        }
        return true;
    }

    public function close()
    {
        foreach (self::$reports as $report) {
            if ($report instanceof Report) {
              /**  DatabaseManager::emptyQuery("INSERT IGNORE INTO reports(id, reporter, reason, reported_person, unix_time) VALUES (?, ?, ?, ?, ?);", Query::SERVER_DB, [
                    $report->getId(),
                    $report->getReporter(),
                    $report->getReason(),
                    $report->getReportedPerson(),
                    $report->getTime()
                ]);**/
                $report->save();
            }
        }
    }
}
