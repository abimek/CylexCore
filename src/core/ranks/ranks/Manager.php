<?php
declare(strict_types=1);

namespace core\ranks\ranks;

use core\main\text\TextFormat;
use core\ranks\Rank;

final class Manager extends Rank
{

    public const RANK_LEVEL = self::MANAGER;
    public const RANK_TYPE = self::STAFF_RANK;
    public const RANK_IDENTIFIER = self::MANAGER_ID;

    /**
     * @return string
     */
    public function getType(): string
    {
        return self::STAFF_RANK;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return self::MANAGER_ID;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return self::MANAGER;
    }

    protected function init(): void
    {
        $this->setChatFormat(TextFormat::BOLD . TextFormat::GOLD . "Manager " . TextFormat::RESET . TextFormat::WHITE . "{name}" . TextFormat::DARK_GRAY . ":" . TextFormat::AQUA . " {msg}");
    }
}