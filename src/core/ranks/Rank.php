<?php
declare(strict_types=1);

namespace core\ranks;

use core\ranks\levels\StaffRankLevels;
use core\ranks\types\RankTypes;
use core\ranks\types\StaffRankIdentifiers;

abstract class Rank implements RankTypes, StaffRankLevels, StaffRankIdentifiers
{

    protected $format;
    protected $display_tag;

    public function __construct()
    {
        $this->init();
    }

    abstract protected function init(): void;

    /**
     * @return string
     */
    abstract function getType(): string;

    /**
     * @return string
     */
    abstract function getIdentifier(): string;

    /**
     * @return int
     */
    abstract function getLevel(): int;

    /**
     * @return mixed
     */
    public function getChatFormat()
    {
        return $this->format;
    }

    /**
     * @return mixed
     */
    public function getDisplayTag()
    {
        return $this->display_tag;
    }

    /**
     * @param string $tag
     */
    public function setDisplayTag(string $tag): void
    {
        $this->display_tag = $tag;
    }

    /**
     * @param string $format
     */
    public function setChatFormat(string $format): void
    {
        $this->format = $format;
    }

}