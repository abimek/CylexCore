<?php
declare(strict_types=1);

namespace core\network\forms\account_forms;

use core\forms\formapi\CustomForm;
use core\main\text\message\Message;
use core\main\text\TextFormat;
use core\network\NetworkManager;
use pocketmine\player\Player;

class ProfileSettingsForm extends CustomForm
{

    private $networkData;

    public function __construct(Player $player)
    {
        parent::__construct($this->getFormResultCallable());
        $this->setTitle(TextFormat::BOLD_GRAY . "Profile-Settings");
        $networkData = NetworkManager::getNetworkPlayerDBHandler()->getPlayerObject($player->getXuid());
        $this->networkData = $networkData;
        if ($networkData === null) {
            $this->addLabel("an error occurred");
            return;
        }
        $this->addLabel(TextFormat::GRAY . "Username: " . $networkData->getUsername());
        $this->addLabel(TextFormat::RED . "Description: " . TextFormat::GRAY . $networkData->getDescription());
        $this->addToggle(TextFormat::GRAY . "Update Description: ");
        $this->addInput(TextFormat::GRAY . "Description: ", $networkData->getDescription());
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?array $data) {
            if ($data === null) {
                return;
            }
            if (!is_bool($data[2])) {
                return;
            }
            if ($data[2] === true) {
                $this->networkData->setDescription($data[3]);
                $player->sendMessage(Message::PREFIX . "You've successfully updates your description!");
            }
        };
    }
}