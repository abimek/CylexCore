<?php
declare(strict_types=1);

namespace core\network\commands;

use core\main\text\TextFormat;
use core\network\forms\account_forms\mail\MailSelectForm;
use core\players\PlayerManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use pocketmine\player\Player;

class MailCommand extends Command
{

    public const NAME = "mail";
    public const DESCRIPTION = "view your mail";
    public const USAGE = TextFormat::RED . "/mail";

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
            $session = PlayerManager::getSession($sender->getXuid());
            if ($session !== null) {
                $form = new MailSelectForm($sender);
                $sender->sendForm($form);
            }
            return;
        }
        return;
    }
}
