<?php
declare(strict_types=1);

namespace core\admin\forms;

use core\admin\forms\mute\MuteSelectForm;
use core\admin\forms\player_info\PlayerInfoSelectForm;
use core\admin\forms\reports\ReportsForm;
use core\admin\forms\warn\WarnSelectForm;
use core\forms\formapi\SimpleForm;
use core\main\text\message\Message;
use core\main\text\TextFormat;
use core\players\PlayerManager;
use core\ranks\levels\StaffRankLevels;
use core\ranks\RankManager;
use pocketmine\player\Player;

class StaffMenuForm extends SimpleForm
{

    public function __construct(Player $player)
    {
        parent::__construct($this->getFormResultCallable());
        $this->setTitle(TextFormat::BOLD_LIGHT_PURPLE . "Admin-" . TextFormat::RED . "Menu");

        $session = PlayerManager::getSession($player->getXuid());

        if ($session === null) {
            return;
        }

        $rank_id = $session->getRankIdentifier();
        $rank = RankManager::getRank($rank_id);

        if ($rank === null) {
            return;
        }
        switch (true) {
            /**
             * case $rank->getLevel() >= StaffRankLevels::ADMIN:
             * $this->addButton(Message::ARROW . TextFormat::GOLD . "Review Bans");
             * $this->addButton(Message::ARROW . TextFormat::GOLD . "Review IpBans");**/
            case $rank->getLevel() >= StaffRankLevels::MOD:
                $this->addButton(Message::ARROW . TextFormat::GOLD . "Player-Info Form");
                $this->addButton(Message::ARROW . TextFormat::GOLD . "Vanish");
                $this->addButton(Message::ARROW . TextFormat::GOLD . "Ban");
                $this->addButton(Message::ARROW . TextFormat::GOLD . "UnBan");
                $this->addButton(Message::ARROW . TextFormat::GOLD . "IpBan");
                $this->addButton(Message::ARROW . TextFormat::GOLD . "UnIpBan");
                $this->addButton(Message::ARROW . TextFormat::GOLD . "Toggle Staff Mode");
                $this->addButton(Message::ARROW . TextFormat::GOLD . "Reports");
            case $rank->getLevel() >= StaffRankLevels::HELPER:
                $this->addButton(Message::ARROW . TextFormat::GOLD . "Warn");
                $this->addButton(Message::ARROW . TextFormat::GOLD . "Mute");
        }
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?int $data) {
            if ($data === null) {
                $player->sendMessage(Message::PREFIX . "You didn't select an option!");
                return;
            }
            $result = $data;
            if ($result === null) {
                $player->sendMessage(Message::PREFIX . "You didn't select an option!");
                return;
            }
            $session = PlayerManager::getSession($player->getXuid());
            if ($session === null) {
                return;
            }
            $rank_id = $session->getRankIdentifier();
            $rank = RankManager::getRank($rank_id);
            if ($rank === null) {
                return;
            }
            switch ($result) {
                /**case 0:
                 * //review bans
                 * break;
                 * case 1:
                 * //review IpBans
                 * break;*/
                case 0:
                    $form = new PlayerInfoSelectForm($player);
                    $player->sendForm($form);
                    break;
                case 1:
                    $form = new VanishForm();
                    $player->sendForm($form);
                    break;
                case 2:
                    $form = new BanForm($player);
                    $player->sendForm($form);
                    break;
                case 3:
                    $form = new UnBanForm($player);
                    $player->sendForm($form);
                    break;
                case 4:
                    $form = new IpBanForm($player);
                    $player->sendForm($form);
                    break;
                case 5:
                    $form = new UnIpBanForm($player);
                    $player->sendForm($form);
                    break;
                case 6:
                    $form = new StaffModeForm();
                    $player->sendForm($form);
                    break;
                case 7:
                    $form = new ReportsForm($player);
                    $player->sendForm($form);
                    break;
                case 8:
                    $form = new WarnSelectForm($player);
                    $player->sendForm($form);
                    break;
                case 9:
                    $form = new MuteSelectForm($player);
                    $player->sendForm($form);
                    break;
                default:
                    $player->sendMessage(Message::PREFIX . "You didn't select an option!");
                    return;
                    break;
            }
            return;
        };
    }
}
