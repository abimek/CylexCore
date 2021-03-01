<?php
declare(strict_types=1);

namespace core\ranks\types;

interface StaffRankIdentifiers
{
    public const OWNER_ID = "Owner";
    public const DEVELOPER_ID = "Developer";
    public const MANAGER_ID = "Manager";
    public const ADMIN_ID = "Admin";
    public const MOD_ID = "Mod";
    public const HELPER_ID = "Helper";
    public const BUILDER_ID = "Builder";
}