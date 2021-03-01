<?php
declare(strict_types=1);

namespace core\network\forms\account_forms;

use core\forms\formapi\CustomForm;
use core\main\text\message\Message;
use core\network\NetworkManager;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class PasswordSettingsForm extends CustomForm
{

    private $networkData;

    public function __construct(Player $player)
    {
        parent::__construct($this->getFormResultCallable());
        $networkData = NetworkManager::getNetworkPlayerDBHandler()->getPlayerObject($player->getXuid());
        $this->networkData = $networkData;
        if ($networkData === null) {
            $this->addLabel("an error occurred");
            return;
        }
        $this->addInput(TextFormat::GRAY . "Change Password: ");
        $this->addInput(TextFormat::GRAY . "Verify Password Change: ");
        $this->addLabel(TextFormat::BOLD . TextFormat::RED . "DONT FORGET YOUR PASSWORD!");
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?array $data) {
            if ($data === null || !isset($data[0]) || $data[0] === "") {
                return;
            }
            if ($data[0] === $data[1] && $data[0] !== "") {
                $this->networkData->setPassword($data[0]);
                $player->sendMessage(Message::PREFIX . "You've successfully changed your password");
                return;
            }
            if ($data[0] !== $data[1]) {
                $player->sendMessage(Message::PREFIX . "You failed to very your password, please try again");
                return;
            }
            return;
        };
    }
}

