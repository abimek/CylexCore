<?php
declare(strict_types=1);

namespace core\vote\forms;

use core\forms\formapi\SimpleForm;
use core\main\text\message\Message;
use core\main\text\TextFormat;
use core\vote\threaded\VoteThread;
use core\vote\VoteManager;
use pocketmine\player\Player;

class VoteForm extends SimpleForm
{

    public function __construct()
    {
        parent::__construct($this->getFormResultCallable());
        $this->setTitle(TextFormat::BOLD_RED . "Vote");
        $this->setContent(TextFormat::GRAY . "Voting helps " . TextFormat::LIGHT_PURPLE . "DraxitePE" . TextFormat::GRAY . "increase in popularity and gain more players, thank you for the support!");
        $this->addButton(TextFormat::BOLD_GRAY . "[" . TextFormat::RESET_RED . "Vote" . TextFormat::BOLD_GRAY . "]");
        $this->addButton(TextFormat::BOLD_GRAY . "[" . TextFormat::RESET_RED . "Top-Voter" . TextFormat::BOLD_GRAY . "]");
        $this->addButton(TextFormat::BOLD_GRAY . "[" . TextFormat::RESET_RED . "VoteInfo" . TextFormat::BOLD_GRAY . "]");
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?int $result) {
            if ($result === null) {
                return;
            }
            switch ($result) {
                case 0:
                    if (VoteManager::getInstance()->getThread()->isActionInQueue(VoteThread::ACTION_VALIDATE_VOTE, $player)) {
                        $player->sendMessage(Message::PREFIX . "You're vote is already being processed!");
                        return false;
                    }
                    VoteManager::getInstance()->getThread()->addActionToQueue(VoteThread::ACTION_VALIDATE_VOTE, $player);
                    $player->sendMessage(Message::PREFIX . "You're vote has began processing");
                    return false;
                    break;
                case 1:
                    $form = new TopVoteForm();
                    $player->sendForm($form);
                    break;
                case 2:
                    $form = new VoteInfoForm();
                    $player->sendForm($form);
                    break;
            }
        };
    }
}