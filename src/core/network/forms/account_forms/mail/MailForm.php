<?php
declare(strict_types=1);

namespace core\network\forms\account_forms\mail;

use core\forms\formapi\CustomForm;
use core\main\text\TextFormat;
use core\network\NetworkManager;
use core\network\objects\Mail;
use pocketmine\player\Player;

class MailForm extends CustomForm
{

    private $networkData;
    private $mail;

    public function __construct(Player $player, Mail $mail)
    {
        $this->mail = $mail;
        parent::__construct($this->getFormResultCallable());
        $this->setTitle(TextFormat::BOLD_GRAY . $mail->getTitle());
        $networkData = NetworkManager::getNetworkPlayerDBHandler()->getPlayerObject($player->getXuid());
        $this->networkData = $networkData;
        if ($networkData === null) {
            $this->addLabel("an error occurred");
            return;
        }
        $this->addLabel(TextFormat::RED . "Date: " . TextFormat::GRAY . date("F j, Y, g:i a", $mail->getTime()));
        $this->addLabel(TextFormat::RED . "Sender: " . $mail->getSender());
        $this->addLabel(TextFormat::LIGHT_PURPLE . "Message: ");
        $this->addLabel(TextFormat::GRAY . $mail->getText());
        $this->addToggle(TextFormat::GRAY . "Delete Mail: ", true);
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?array $data) {
            if ($data === null) {
                return;
            }
            if (!is_bool($data[4])) {
                return;
            }
            if ($data[4] === true) {
                $this->networkData->removeMail($this->mail->getId());
            }
        };
    }
}
