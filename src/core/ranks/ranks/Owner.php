<?php
declare(strict_types=1);

namespace core\ranks\ranks;

use core\main\text\TextFormat;
use core\ranks\Rank;

final class Owner extends Rank
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
        return self::OWNER_ID;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return self::OWNER;
    }

    protected function init(): void
    {
        $this->setChatFormat(TextFormat::BOLD . TextFormat::RED . "Owner " . TextFormat::RESET . TextFormat::DARK_PURPLE . "{name}" . TextFormat::DARK_GRAY . ":" . TextFormat::YELLOW . " {msg}");
    }
}