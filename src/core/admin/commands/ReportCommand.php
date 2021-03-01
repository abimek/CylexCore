<?php
declare(strict_types=1);

namespace core\admin\commands;

use core\admin\forms\reports\ReportPlayerSelectForm;
use core\main\text\TextFormat;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use pocketmine\player\Player;

class ReportCommand extends Command
{

    public const NAME = "report";
    public const DESCRIPTION = "brings up the report form";
    public const USAGE = TextFormat::RED . "/report";

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
            $form = new ReportPlayerSelectForm($sender);
            $sender->sendForm($form);
            return;
        }
    }
}
