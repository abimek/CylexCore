<?php
declare(strict_types=1);

namespace core\admin\commands;

use core\admin\handlers\MuteHandler;
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

class UnMuteCommand extends Command
{

    public const NAME = "unmute";
    public const DESCRIPTION = "brings up the mute form";
    public const USAGE = TextFormat::RED . "/mute <name>";

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
                $sender->sendMessage(self::USAGE);
                return;
            }
            $name = $args[0];
            PlayerManager::getDatabaseHandler()->getPlayerObjectByUsername($name, function ($object) use ($sender, $name) {
                if ($object instanceof PlayerObject) {
                    if ($name === null) {
                        $sender->sendMessage(Message::PREFIX . "The player by the name " . TextFormat::LIGHT_PURPLE . $name . TextFormat::GRAY . " does not exist!");
                        return;
                    }
                    MuteHandler::unMutePlayer($object);
                    $sender->sendMessage(Message::PREFIX . "Successfully unmuted " . TextFormat::LIGHT_PURPLE . $name . "!");
                    $session = PlayerManager::getSessionByUsername($name);
                    if ($session === null) {
                        return;
                    }
                    $session->getPlayer()->sendMessage(Message::PREFIX . "You were unmuted by " . TextFormat::LIGHT_PURPLE . $sender->getName());
                    return;
                }
            });
        }
        return;
    }
}
