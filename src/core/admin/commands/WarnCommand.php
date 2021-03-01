<?php
declare(strict_types=1);

namespace core\admin\commands;

use core\admin\forms\warn\WarnSelectForm;
use core\admin\handlers\WarnHandler;
use core\admin\messages\AdminMessageType;
use core\main\text\message\Message;
use core\main\text\TextFormat;
use core\players\objects\PlayerObject;
use core\players\PlayerManager;
use core\ranks\levels\StaffRankLevels;
use core\ranks\RankManager;
use core\ranks\types\RankTypes;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use pocketmine\player\Player;

class WarnCommand extends Command
{

    public const NAME = "warn";
    public const DESCRIPTION = "brings up the warn form";
    public const USAGE = TextFormat::RED . "/warn <player> <reason>";

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
            if ($rank->getLevel() < StaffRankLevels::HELPER) {
                $sender->sendMessage(Message::getMessage(AdminMessageType::IDENTIFIER, AdminMessageType::TYPE_ONLY_ADMIN, ["{name}" => self::NAME]));
                return;
            }
            if (!isset($args[0])) {
                $form = new WarnSelectForm($sender);
                $sender->sendForm($form);
                return;
            } else {
                if (!isset($args[1])) {
                    $sender->sendMessage(self::USAGE);
                    return;
                }
                PlayerManager::getDatabaseHandler()->getPlayerObjectByUsername($args[0], function ($object) use ($sender, $session, $args) {
                    if ($object instanceof PlayerObject) {
                        if ($object === null || (($rank = RankManager::getRank($object->getRank())) !== null && $rank->getLevel() === StaffRankLevels::OWNER)) {
                            $sender->sendMessage(Message::PREFIX . "The person (" . \pocketmine\utils\TextFormat::LIGHT_PURPLE . $args[0] . TextFormat::GRAY . ") seems not to exist!");
                            return;
                        }
                        WarnHandler::warnPlayer($session, $args[1]);
                        $sender->sendMessage(Message::PREFIX . "Successfully warned the player " . \pocketmine\utils\TextFormat::RED . $args[0]);
                    }
                });
            }
            return;
        }
        return;
    }
}
