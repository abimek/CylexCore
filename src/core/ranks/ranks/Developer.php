<?php
declare(strict_types=1);

namespace core\ranks\ranks;

use core\main\text\TextFormat;
use core\ranks\Rank;

final class Developer extends Rank
{


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
        return self::DEVELOPER_ID;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return self::DEVELOPER;
    }

    protected function init(): void
    {
        $this->setChatFormat(TextFormat::BOLD . TextFormat::YELLOW . "Developer " . TextFormat::RESET . TextFormat::WHITE . "{name}" . TextFormat::DARK_GRAY . ":" . TextFormat::AQUA . " {msg}");
    }
}