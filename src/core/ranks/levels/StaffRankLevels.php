<?php
declare(strict_types=1);

namespace core\ranks\levels;

interface StaffRankLevels
{
    public const OWNER = 7;
    public const DEVELOPER = 6;
    public const MANAGER = 5;
    public const ADMIN = 4;
    public const MOD = 3;
    public const HELPER = 2;
    public const BUILDER = 1;
}