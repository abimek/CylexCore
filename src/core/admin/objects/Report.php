<?php
declare(strict_types=1);

namespace core\admin\objects;

use core\main\data\formatter\JsonFormatter;

class Report
{

    use JsonFormatter;

    private $being_handeled = false;
    private $id;
    private $reporter;
    private $reason;
    private $reported_person;
    private $time;

    public function __construct(string $id, string $reporter, string $reason, string $reported_person, int $time)
    {
        $this->id = $id;
        $this->reporter = $reporter;
        $this->reason = $reason;
        $this->reported_person = $reported_person;
        $this->time = $time;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getReporter(): string
    {
        return $this->reporter;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function getReportedPerson(): string
    {
        return $this->reported_person;
    }

    public function isBeingHandled(): bool
    {
        return $this->being_handeled;
    }

    public function setBeingHandled(bool $value)
    {
        $this->being_handeled = $value;
    }

    public function getTime(): int
    {
        return $this->time;
    }

    public function decodeData(): string
    {
        $data = [
            "reporter" => $this->reporter,
            "reason" => $this->reason,
            "reported_person" => $this->reported_person,
            "time" => $this->time
        ];
        return $this->encodeJson($data);
    }
}