<?php
declare(strict_types=1);

namespace core\network\forms\account_forms;

use core\forms\formapi\CustomForm;
use core\main\text\TextFormat;
use core\network\forms\VerifyForm;
use core\network\NetworkManager;
use pocketmine\player\Player;

class PrivateSettingsForm extends CustomForm
{

    private $networkData;

    public function __construct(Player $player)
    {
        parent::__construct($this->getFormResultCallable());
        $this->setTitle(TextFormat::BOLD_GRAY . "Private-Settings");
        $networkData = NetworkManager::getNetworkPlayerDBHandler()->getPlayerObject($player->getXuid());
        $this->networkData = $networkData;
        if ($networkData === null) {
            $this->addLabel("an error occurred");
            return;
        }
        //$this->addToggle(TextFormat::GRAY . "Ip-Locked", $networkData->isIpLocked());
        $this->addLabel(TextFormat::GRAY . "Ip Locking Coming Soon!");
        $this->addToggle(TextFormat::GRAY . "Password Verification: you must input your password when you log in", $networkData->isPasswordLocked());
        $this->addToggle(TextFormat::GRAY . "Password Info: takes you to the password settings form once you submit");
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?array $data) {
            if ($data === null) {
                return;
            }
            if (!is_bool($data[0])) {
                return;
            }
            //$this->networkData->setIpLocked($data[0]);
            $this->networkData->setPasswordLocked($data[1]);
            if ($data[2] === true) {
                if ($this->networkData->getPassword() === "") {
                    $form = new PasswordSettingsForm($player);
                    $player->sendForm($form);
                    return;
                }
                $form = new VerifyForm($player, function (Player $p) {
                    $form = new PasswordSettingsForm($p);
                    $p->sendForm($form);
                });
                $player->sendForm($form);
            }
        };
    }
}