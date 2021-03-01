<?php
declare(strict_types=1);

namespace core\admin;

use core\admin\commands\BanCommand;
use core\admin\commands\IpBanCommand;
use core\admin\commands\MuteCommand;
use core\admin\commands\PlayerInfoCommand;
use core\admin\commands\ReportCommand;
use core\admin\commands\ReportsCommand;
use core\admin\commands\StaffMenuCommand;
use core\admin\commands\StaffModeCommand;
use core\admin\commands\UnBanCommand;
use core\admin\commands\UnIpBanCommand;
use core\admin\commands\UnMuteCommand;
use core\admin\commands\VanishCommand;
use core\admin\commands\WarnCommand;
use core\admin\database\BanDatabaseHandler;
use core\admin\database\IpBanDatabaseHandler;
use core\admin\database\ReportDatabaseHandler;
use core\admin\listeners\PlayerChatListener;
use core\admin\listeners\PlayerPreLoinListener;
use core\admin\listeners\StaffModeListener;
use core\admin\messages\AdminMessageType;
use core\main\managers\Manager;
use core\main\text\message\Message;

class AdminManager extends Manager
{

    /**
     * @var BanDatabaseHandler
     */
    private $banDBHandler;

    /**
     * @var IpBanDatabaseHandler
     */
    private $ipBanDBHandler;

    /**
     * @var ReportDatabaseHandler
     */
    private $reportDBHandler;

    protected function init(): void
    {
        $this->registerCommands();
        Message::registerType(new AdminMessageType());
        $this->banDBHandler = new BanDatabaseHandler();
        $this->ipBanDBHandler = new IpBanDatabaseHandler();
        $this->reportDBHandler = new ReportDatabaseHandler();
        $this->registerListener(new PlayerPreLoinListener());
        $this->registerListener(new PlayerChatListener());
        $this->registerListener(new StaffModeListener());
    }

    private function registerCommands(): void
    {
        $map = $this->getCore()->getServer()->getCommandMap();
        $map->register("ban", new BanCommand());
        $map->register("unban", new UnBanCommand());
        $map->register("ipban", new IpBanCommand());
        $map->register("unipban", new UnIpBanCommand());
        $map->register("pinfo", new PlayerInfoCommand());
        $map->register("report", new ReportCommand());
        $map->register("reports", new ReportsCommand());
        $map->register("staffmenu", new StaffMenuCommand());
        $map->register("mute", new MuteCommand());
        $map->register("unmute", new UnMuteCommand());
        $map->register("warn", new WarnCommand());
        $map->register("vanish", new VanishCommand());
        $map->register("staffmode", new StaffModeCommand());
    }

    protected function close(): void
    {
        $this->banDBHandler->close();
        $this->ipBanDBHandler->close();
        $this->reportDBHandler->close();
    }

}