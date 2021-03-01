<?php
declare(strict_types=1);

namespace core\network\forms\account_forms\mail;

use core\forms\formapi\SimpleForm;
use core\main\text\message\Message;
use core\main\text\TextFormat;
use core\network\NetworkManager;
use core\network\objects\Mail;
use pocketmine\player\Player;

class MailSelectForm extends SimpleForm
{

    private $networkData;

    private $mail = [];

    public function __construct(Player $player)
    {
        parent::__construct($this->getFormResultCallable());

        $networkData = NetworkManager::getNetworkPlayerDBHandler()->getPlayerObject($player->getXuid());
        if ($networkData === null) {
            $this->setContent(TextFormat::BOLD_GRAY . "An error has occurred!");
            return;
        }
        $this->networkData = $networkData;
        if (count($networkData->getMail()) === 0) {
            $this->setContent(TextFormat::GRAY . "You currently do not have any mail, please check your mail at a later time!");
        }
        $this->setTitle(TextFormat::BOLD_RED . "Mail" . TextFormat::GRAY . "[" . count($networkData->getMail()) . "]");
        $this->addButton(Message::PREFIX . "Compose");
        foreach ($networkData->getMail() as $mail) {
            $title = $mail[1];
            $this->mail[] = $mail[0];
            $this->addButton($title);
        }
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?int $result) {
            if ($result === null) {
                return;
            }
            if ($result === 0) {
                $form = new MailComposeForm($player);
                $player->sendForm($form);
                return;
            }
            $mail = $this->networkData->getMail()[$this->mail[$result - 1]];
            $mail = Mail::mailFromData($mail);
            $form = new MailForm($player, $mail);
            $player->sendForm($form);
        };
    }
}