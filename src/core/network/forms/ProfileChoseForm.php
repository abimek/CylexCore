<?php
declare(strict_types=1);

namespace core\network\forms;

use core\forms\formapi\builtin\PlayerSelectForm;
use core\forms\formapi\ModalForm;
use core\main\text\message\Message;
use core\main\text\TextFormat;
use core\network\NetworkManager;
use core\network\objects\NetworkPlayer;
use core\players\objects\PlayerObject;
use core\players\PlayerManager;
use pocketmine\player\Player;

class ProfileChoseForm extends ModalForm
{

    public function __construct()
    {
        parent::__construct($this->getFormResultCallable());
        $this->setTitle(TextFormat::GRAY . "Profile Select");
        $this->setContent(TextFormat::GRAY . "Select " . TextFormat::RED . "My-Own" . TextFormat::GRAY . " if you would like to see your profile or select " . TextFormat::RED . "Someone-else's" . TextFormat::GRAY . " if you would like to see someone else's profile!");
        $this->setButton1(TextFormat::LIGHT_PURPLE . "My-Own");
        $this->setButton2(TextFormat::LIGHT_PURPLE . "Someone-else's");
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?bool $data) {
            if ($data === null) {
                return;
            }
            if ($data === true) {
                $form = new ProfileForm($player, NetworkManager::getNetworkPlayerDBHandler()->getPlayerObject($player->getXuid()));
                $player->sendForm($form);
            } else {
                $form = new PlayerSelectForm($player, function (Player $player, ?array $data) {
                    if ($data === null) {
                        $player->sendMessage(Message::PREFIX . "No player was selected");
                    }
                    $name = $data[0];
                    PlayerManager::getDatabaseHandler()->getPlayerObjectByUsername($name, function ($object) use ($player) {
                        if ($object instanceof PlayerObject) {
                            NetworkManager::getNetworkPlayerDBHandler()->loadAccountAndCallable($object->getXuid(), function (NetworkPlayer $player1) use ($player) {
                                $player->sendForm(new ProfileForm($player, $player1));
                            });
                            return;
                        }
                        $player->sendMessage(Message::PREFIX . "Player seems to not exist!");
                    });
                });
                $player->sendForm($form);
            }
        };
    }
}