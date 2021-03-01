<?php
declare(strict_types=1);

namespace core\network\forms\account_forms;

use core\forms\formapi\CustomForm;
use core\main\text\message\Message;
use core\main\text\TextFormat;
use core\network\NetworkManager;
use pocketmine\player\Player;

class SocialSettingsForm extends CustomForm
{

    private $networkData;

    public function __construct(Player $player)
    {
        parent::__construct($this->getFormResultCallable());
        $this->setTitle(TextFormat::BOLD_GRAY . "Social-Settings");
        $networkData = NetworkManager::getNetworkPlayerDBHandler()->getPlayerObject($player->getXuid());
        $this->networkData = $networkData;
        if ($networkData === null) {
            $this->addLabel("an error occurred");
            return;
        }
        $this->addLabel(TextFormat::GRAY . "Username: " . $networkData->getUsername());
        $this->addLabel(TextFormat::RED . "Discord: " . TextFormat::GRAY . $networkData->getDiscord());
        $this->addLabel(TextFormat::RED . "Youtube: " . TextFormat::GRAY . $networkData->getYoutube());
        $this->addToggle(TextFormat::GRAY . "Update Discord: ");
        $this->addInput(TextFormat::GRAY . "Discord: ", $networkData->getDescription());
        $this->addToggle(TextFormat::GRAY . "Update Youtube: ");
        $this->addInput(TextFormat::GRAY . "Youtube: ", $networkData->getDescription());

    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?array $data) {
            if ($data === null) {
                return;
            }
            if (!is_bool($data[3])) {
                return;
            }
            if ($data[3] === true) {
                $this->networkData->setDiscord($data[4]);
                $player->sendMessage(Message::PREFIX . "Successfully changed your discord to " . TextFormat::LIGHT_PURPLE . $data[4]);
            }
            if ($data[5] === true) {
                $this->networkData->setYoutube($data[6]);
                $player->sendMessage(Message::PREFIX . "Successfully changed your youtube to " . TextFormat::LIGHT_PURPLE . $data[6]);
            }
        };
    }
}