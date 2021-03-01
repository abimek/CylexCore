<?php
declare(strict_types=1);

namespace core\vote\commands;

use core\vote\forms\VoteForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use pocketmine\player\Player;

class VoteCommand extends Command
{
    public const NAME = "vote";
    public const DESCRIPTION = "claim your vote";
    public const ALIASES = [];
    public const USAGE = "/vote";

    public function __construct()
    {
        parent::__construct(self::NAME, self::DESCRIPTION, "", self::ALIASES);
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
            $form = new VoteForm();
            $sender->sendForm($form);
        }
        return;
    }
}