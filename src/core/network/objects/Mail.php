<?php
declare(strict_types=1);

namespace core\network\objects;

use core\main\text\TextFormat;

class Mail
{

    public const DRAXITE_SENDER = TextFormat::BOLD_LIGHT_PURPLE . "Draxite" . TextFormat::GRAY . "PE";

    private $id;
    private $time;
    private $title;
    private $text;
    private $sender;

    public function __construct(string $id, int $time, string $title, string $text, string $sender)
    {
        $this->id = $id;
        $this->time = $time;
        $this->title = $title;
        $this->text = $text;
        $this->sender = $sender;
    }

    public static function mailFromData(array $data): Mail
    {
        return new Mail($data[0], $data[2], $data[1], $data[3], $data[4]);
    }

    public function encodeData(): array
    {
        return [$this->getId(), $this->getTitle(), $this->getTime(), $this->getText(), $this->getSender()];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getTime(): int
    {
        return $this->time;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getSender(): string
    {
        return $this->sender;
    }
}