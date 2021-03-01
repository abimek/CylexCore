<?php
declare(strict_types=1);

namespace core\network\commands;

use core\main\text\message\Message;
use core\main\text\TextFormat;
use core\network\forms\ProfileChoseForm;
use core\network\forms\ProfileForm;
use core\network\NetworkManager;
use core\network\objects\NetworkPlayer;
use core\players\objects\PlayerObject;
use core\players\PlayerManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use pocketmine\player\Player;

class ProfileCommand extends Command
{

    public const NAME = "profile";
    public const DESCRIPTION = "view someone's profile";
    public const USAGE = TextFormat::RED . "/profile <player>";

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
            if (!isset($args[0])) {
                $form = new ProfileChoseForm();
                $sender->sendForm($form);
                return;
            }
            $name = $args[0];
            PlayerManager::getDatabaseHandler()->getPlayerObjectByUsername($name, function ($object) use ($sender) {
                if ($object instanceof PlayerObject) {
                    NetworkManager::getNetworkPlayerDBHandler()->loadAccountAndCallable($object->getXuid(), function (NetworkPlayer $player1) use ($sender) {
                        $sender->sendForm(new ProfileForm($sender, $player1));
                    });
                    return;
                }
                $sender->sendMessage(Message::PREFIX . "Player seems to not exist!");
            });
        }
        return;
    }
}