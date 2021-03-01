<?php
declare(strict_types=1);

namespace core\network\forms;

use core\forms\formapi\SimpleForm;
use core\main\text\TextFormat;
use core\network\forms\account_forms\mail\MailSelectForm;
use core\network\forms\account_forms\PrivateSettingsForm;
use core\network\forms\account_forms\ProfileSettingsForm;
use core\network\forms\account_forms\SocialSettingsForm;
use core\network\NetworkManager;
use pocketmine\player\Player;

class AccountSettingsForm extends SimpleForm
{

    public function __construct(Player $player)
    {
        parent::__construct($this->getFormResultCallable());

        $networkData = NetworkManager::getNetworkPlayerDBHandler()->getPlayerObject($player->getXuid());
        if ($networkData === null) {
            $this->setContent(TextFormat::BOLD_GRAY . "An error has occurred!");
            return;
        }
        $this->setTitle(TextFormat::BOLD_DARK_GRAY . "Account Settings");
        $this->addButton(TextFormat::BOLD_DARK_GRAY . "Private");
        $this->addButton(TextFormat::BOLD_DARK_GRAY . "Profile");
        $this->addButton(TextFormat::BOLD_DARK_GRAY . "Social");
        $mail_amount = count($networkData->getMail());
        $this->addButton(TextFormat::BOLD_DARK_GRAY . "Mail " . TextFormat::RED . "[" . TextFormat::LIGHT_PURPLE . $mail_amount . TextFormat::RED . "]");
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?int $result) {
            if ($result === null) {
                return;
            }
            switch ($result) {
                case 0:
                    $form = new PrivateSettingsForm($player);
                    $player->sendForm($form);
                    return;
                case 1:
                    $form = new ProfileSettingsForm($player);
                    $player->sendForm($form);
                    return;
                case 2:
                    $form = new SocialSettingsForm($player);
                    $player->sendForm($form);
                    return;
                case 3:
                    $form = new MailSelectForm($player);
                    $player->sendForm($form);
                    return;
            }
        };
    }
}