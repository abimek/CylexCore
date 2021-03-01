<?php
declare(strict_types=1);

namespace core\admin\forms;

use core\admin\handlers\BanHandler;
use core\forms\formapi\CustomForm;
use core\main\text\message\Message;
use core\players\PlayerManager;
use core\ranks\levels\StaffRankLevels;
use core\ranks\RankManager;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class UnBanForm extends CustomForm
{

    public function __construct(Player $player)
    {
        $session = PlayerManager::getSession($player->getXuid());
        if ($session === null) {
            return;
        }
        $rank_id = $session->getRankIdentifier();
        $rank = RankManager::getRank($rank_id);
        if ($rank === null) {
            return;
        }
        parent::__construct($this->getFormResultCallable());
        if ($rank->getLevel() >= StaffRankLevels::MOD) {
            $this->setTitle(TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "UnBan");
            $this->addInput(Message::ARROW . TextFormat::RED . "Player-Name", "", "");
        }
    }


    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?array $data = null) {
            if ($data === null) {
                return;
            }
            if ($data[0] === "") {
                $player->sendMessage(Message::PREFIX . "you inserted invalid parameters and were-not able to do a successful ban, please try again!");
                return;
            }
            $player_name = $data[0];
            BanHandler::unBanPlayer($player_name);
            $player->sendMessage(Message::PREFIX . "Successfully unbanned " . TextFormat::LIGHT_PURPLE . $player_name);
            return;
        };
    }
}