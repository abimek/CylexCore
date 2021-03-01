<?php
declare(strict_types=1);

namespace core\ranks\ranks;

use core\main\text\TextFormat;
use core\ranks\Rank;

final class Rookie extends Rank
{
    public const ROOKIE = "Rookie";

    /**
     * @return string
     */
    public function getType(): string
    {
        return self::NORMAL_RANK;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return self::ROOKIE;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return 1;
    }

    protected function init(): void
    {
        $this->setChatFormat(TextFormat::BOLD . TextFormat::GRAY . "Rookie " . TextFormat::RESET . TextFormat::WHITE . "{name}" . TextFormat::DARK_GRAY . ":" . TextFormat::GRAY . " {msg}");
    }
}