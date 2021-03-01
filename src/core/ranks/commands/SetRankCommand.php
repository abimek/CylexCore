<?php
declare(strict_types=1);

namespace core\ranks\commands;

use core\main\text\message\Message;
use core\main\text\TextFormat;
use core\players\objects\PlayerObject;
use core\players\PlayerManager;
use core\ranks\RankHandler;
use core\ranks\RankManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\utils\CommandException;
use pocketmine\player\Player;
use pocketmine\Server;

class SetRankCommand extends Command
{

    public const USAGE = TextFormat::RED . "/setrank <name> <rank> <optional:buycraft_purchase>";

    public function __construct()
    {
        parent::__construct("setrank", "Set a players rank", null, []);
    }

    /**
     * @param string[] $args
     *
     * @return mixed
     * @throws CommandException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof ConsoleCommandSender || $sender->getName() === "ScarceityPvP") {
            if (isset($args[0]) && isset($args[1])) {
                $player_name = $args[0];
                $rank_name = $args[1];
                $rank = RankManager::getRank($rank_name);
                PlayerManager::getDatabaseHandler()->getPlayerObjectByUsername($player_name, function ($object) use ($player_name, $sender, $rank, $rank_name) {
                    if ($object instanceof PlayerObject) {
                        if ($object === null) {
                            $sender->sendMessage(Message::PREFIX . "A player by the name (" . TextFormat::LIGHT_PURPLE . $player_name . TextFormat::GRAY . ") does not exist!");
                            return;
                        }
                        if ($rank === null) {
                            $sender->sendMessage(Message::PREFIX . "A rank by the identifier (" . TextFormat::LIGHT_PURPLE . $rank_name . TextFormat::GRAY . ") does not exist!");
                            return;
                        }
                        if ($object instanceof PlayerObject) {
                            RankHandler::setRank($object, $rank);
                            $player = Server::getInstance()->getPlayerExact($player_name);
                            if (!isset($args[2])) {
                                if ($player instanceof Player) {
                                    $player->sendMessage(Message::PREFIX . "The rank " . TextFormat::LIGHT_PURPLE . $rank->getIdentifier() . TextFormat::GRAY . " has been applied!");
                                }
                                $sender->sendMessage(Message::PREFIX . "The player (" . TextFormat::LIGHT_PURPLE . $player_name . TextFormat::GRAY . ") has been assigned the rank (" . TextFormat::LIGHT_PURPLE . $rank_name . TextFormat::GRAY . ")!");
                            } else {
                                if ($player instanceof Player) {
                                    $player->sendMessage(Message::PREFIX . "You have just purchased the rank " . TextFormat::LIGHT_PURPLE . $rank->getIdentifier() . TextFormat::GRAY . "!");
                                }
                                Server::getInstance()->broadcastMessage(TextFormat::BOLD . TextFormat::RED . "> " . TextFormat::RESET_GOLD . $player_name . TextFormat::GRAY . " has just purchased the " . TextFormat::BOLD_LIGHT_PURPLE . $rank_name . TextFormat::RESET_GRAY . " rank!");
                            }
                        }
                    }
                });
            } else {
                $sender->sendMessage(self::USAGE);
            }
        }
        return;
    }
}