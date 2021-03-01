<?php
declare(strict_types=1);

namespace core\admin\forms\player_info;

use core\forms\formapi\CustomForm;
use core\main\text\message\Message;
use core\players\objects\PlayerObject;
use core\players\PlayerManager;
use core\ranks\levels\StaffRankLevels;
use core\ranks\RankManager;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class PlayerInfoSelectForm extends CustomForm
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
            $this->setTitle(TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Player-Info Select");
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
            PlayerManager::getDatabaseHandler()->getPlayerObjectByUsername($data[0], function ($object) use ($player, $data) {
                if ($object instanceof PlayerObject) {
                    if ($object === null || (($rank = RankManager::getRank($object->getRank())) !== null && $rank->getLevel() === StaffRankLevels::OWNER)) {
                        $player->sendMessage(Message::PREFIX . "The person (" . TextFormat::LIGHT_PURPLE . $data[0] . TextFormat::GRAY . ") seems not to exist!");
                        return;
                    }
                }
                $form = new PlayerInfoForm($player, $object);
                $player->sendForm($form);
                return;
            });
        };
    }
}
