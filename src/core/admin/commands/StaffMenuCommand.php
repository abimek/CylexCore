<?php
declare(strict_types=1);

namespace core\admin\commands;

use core\admin\forms\StaffMenuForm;
use core\main\text\TextFormat;
use core\players\PlayerManager;
use core\ranks\RankManager;
use core\ranks\types\RankTypes;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use pocketmine\player\Player;

class StaffMenuCommand extends Command
{

    public const NAME = "staffmenu";
    public const DESCRIPTION = "brings up the staffmenu";
    public const USAGE = TextFormat::RED . "/staffmenu";

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
            if ($session === null) {
                return;
            }
            $rank_id = $session->getRankIdentifier();
            $rank = RankManager::getRank($rank_id);
            if ($rank === null || $rank->getType() === RankTypes::NORMAL_RANK) {
                return;
            }
            $form = new StaffMenuForm($sender);
            $sender->sendForm($form);
            return;
        }
        return;
    }
}
