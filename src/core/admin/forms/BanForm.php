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

class BanForm extends CustomForm
{

    private $person;

    public function __construct(Player $player, ?string $person = null)
    {
        $this->person = $person;
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
            $this->setTitle(TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Ban");
            if ($person === null) {
                $this->addInput(Message::ARROW . TextFormat::RED . "Player-Name", "", "");
            } else {
                $this->addLabel(Message::ARROW . TextFormat::RED . "Player-Name" . " " . $person);
            }
            $this->addInput(Message::ARROW . TextFormat::RED . "Reason", "", "");
        }
    }


    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?array $data = null) {
            if ($data === null) {
                return;
            }
            if ($data[0] === "" || $data[1] === "") {
                $player->sendMessage(Message::PREFIX . "you inserted invalid parameters and were-not able to do a successful ban, please try again!");
                return;
            }
            if (is_string($data[0])) {
                $player_name = $data[0];
            } else {
                $player_name = $this->person;
            }
            $reason = $data[1];
            $banner = $player->getName();
            if (BanHandler::banPlayer($player_name, $reason, $banner) === true) {
                $player->sendMessage(Message::PREFIX . "Successfully banned " . TextFormat::LIGHT_PURPLE . $player_name . TextFormat::GRAY . " for " . TextFormat::RED . $reason);
            }
            return;
        };
    }
}