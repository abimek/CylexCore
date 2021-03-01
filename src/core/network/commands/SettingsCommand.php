<?php
declare(strict_types=1);

namespace core\network\commands;

use core\main\text\TextFormat;
use core\network\forms\AccountSettingsForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use pocketmine\player\Player;

class SettingsCommand extends Command
{

    public const NAME = "settings";
    public const DESCRIPTION = "view your network settings";
    public const USAGE = TextFormat::RED . "/settings";

    public function __construct()
    {
        parent::__construct(self::NAME, self::DESCRIPTION, null, []);
    }

    /**
     * @param string[] $args
     *
     * @return mixed
     * @throws CommandException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            $form = new AccountSettingsForm($sender);
            $sender->sendForm($form);
            return;
        }
        return;
    }
}
