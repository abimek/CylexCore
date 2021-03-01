<?php
declare(strict_types=1);

namespace core\admin\commands;

use core\admin\forms\UnIpBanForm;
use core\admin\handlers\IpBanHandler;
use core\admin\messages\AdminMessageType;
use core\main\text\message\Message;
use core\main\text\TextFormat;
use core\players\PlayerManager;
use core\ranks\levels\StaffRankLevels;
use core\ranks\RankManager;
use core\ranks\types\RankTypes;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use pocketmine\player\Player;

class UnIpBanCommand extends Command
{

    public const NAME = "unipban";
    public const DESCRIPTION = "brings up the un-ip-ban form";
    public const USAGE = TextFormat::RED . "/unipban <name>";

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
            if ($rank->getLevel() < StaffRankLevels::MOD) {
                $sender->sendMessage(Message::getMessage(AdminMessageType::IDENTIFIER, AdminMessageType::TYPE_ONLY_ADMIN, ["{name}" => self::NAME]));
                return;
            }
        }
        if (isset($args[0])) {
            IpBanHandler::unIpBanPlayer($args[0]);
            $sender->sendMessage(Message::PREFIX . "Un-ip-banned a player by the name (" . TextFormat::LIGHT_PURPLE . $args[0] . TextFormat::GRAY . ")");
        } else {
            if ($sender instanceof Player) {
                $form = new UnIpBanForm($sender);
                $sender->sendForm($form);
                return;
            } else {
                $sender->sendMessage(self::USAGE);
                return;
            }
        }
        return;
    }
}
