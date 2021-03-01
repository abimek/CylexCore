<?php
declare(strict_types=1);

namespace core\main\commands;

use core\CylexCore;
use core\main\text\TextFormat;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

abstract class BaseCommand extends Command
{

    /**
     * @var array
     */
    private $subCommands = [];
    private $enable_help = false;

    public function __construct(string $name, string $description, string $usageMessage, array $aliases, bool $enable_help = false)
    {
        $this->enable_help = $enable_help;
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->initSubCommands();
    }

    abstract public function initSubCommands();

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return bool|mixed
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (isset($args[0]) && isset($this->subCommands[$args[0]])) {
            $cmd = $this->subCommands[$args[0]];
            if ($cmd instanceof SubCommand) {
                $args1 = [];
                $isset = false;
                foreach ($args as $arg) {
                    if ($isset === false) {
                        $isset = true;
                        continue;
                    } else {
                        $args1[] = $arg;
                    }
                }
                $cmd->execute($sender, $args1);
                return true;
            }
            $sender->sendMessage($this->getUsage());
        } else {
            if ($this->enable_help === true) {
                $count = 0;
                $keys = [];
                foreach ($this->subCommands as $name => $subCommand) {
                    $count++;
                    $keys[$subCommand->getId] = $subCommand->getDescription();
                }
                $max_page = ceil($count / 10);
                $page = 1;
                if (isset($args[0])) {
                    if (is_int($args[0])) {
                        $page = intval($args[0]);
                        if ($page === 0) {
                            $page = 1;
                        }
                    }
                }

                if ($page > $max_page) {
                    $page = $max_page;
                }
                $msg = TextFormat::GRAY . "Help for the command " . TextFormat::RED . "/" . $this->getName() . "Page " . TextFormat::LIGHT_PURPLE . "$page " . TextFormat::GRAY . "of " . TextFormat::LIGHT_PURPLE . $max_page;
                $max_per_page = 10;
                $commands = array_slice($keys, ($page * $max_per_page) - $max_per_page, $max_per_page);
                $sender->sendMessage($msg);
                foreach ($commands as $c => $description) {
                    $sender->sendMessage(TextFormat::YELLOW . "($c): $description");
                }
            }
        }
        return false;
    }

    /**
     * @param SubCommand $command
     */
    public function registerSubCommand(SubCommand $command)
    {
        foreach ($command->getNames() as $name) {
            $this->subCommands[$name] = $command;
        }
    }

    /**
     * @return array
     */
    public function getSubCommands(): array
    {
        return $this->subCommands;
    }

    /**
     * @return CylexCore
     */
    public function getCore(): CylexCore
    {
        return CylexCore::getInstance();
    }
}
