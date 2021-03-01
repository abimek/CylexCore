<?php
declare(strict_types=1);

namespace core\admin\commands;

use core\admin\forms\IpBanForm;
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

class IpBanCommand extends Command
{

    public const NAME = "ipban";
    public const DESCRIPTION = "brings up the ipban form";
    public const USAGE = TextFormat::RED . "/ipban <name> <reason>";

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
            if (!isset($args[1])) {
                $sender->sendMessage(self::USAGE);
                return;
            }
            if (IpBanHandler::ipBanPlayer($args[0], $args[1], $sender->getName()) === true) {
                $sender->sendMessage(Message::PREFIX . "Successfully ipbanned " . TextFormat::LIGHT_PURPLE . $args[0] . TextFormat::GRAY . " for " . TextFormat::RED . $args[1]);
                return;
            }
            $sender->sendMessage(Message::PREFIX . "Unable to ipban the player " . TextFormat::LIGHT_PURPLE . $args[0]);
        } else {
            if ($sender instanceof Player) {
                $form = new IpBanForm($sender);
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