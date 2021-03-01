<?php
declare(strict_types=1);

namespace core\main\commands;

use core\main\text\message\Message;
use core\main\text\TextFormat;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use pocketmine\player\Player;

abstract class Command extends \pocketmine\command\Command
{

    public const NAME = "";
    public const DESCRIPTION = "";
    public const USAGE = "";
    public const USAGE_MESSAGE = null;
    public const ALIASES = [];

    public function __construct()
    {
        parent::__construct(self::NAME, self::DESCRIPTION, self::USAGE_MESSAGE, self::ALIASES);
    }

    /**
     * @param string[] $args
     *
     * @return mixed
     * @throws CommandException
     */
    abstract public function execute(CommandSender $sender, string $commandLabel, array $args);

    public function sendNoPermissionMessage(Player $player)
    {
        $player->sendMessage(Message::PREFIX . "Sadly :(... you do not have permission to use the command (" . TextFormat::LIGHT_PURPLE . self::NAME . TextFormat::GRAY . ")");
    }
}