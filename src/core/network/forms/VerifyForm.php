<?php
declare(strict_types=1);

namespace core\network\forms;

use core\forms\formapi\CustomForm;
use core\main\text\message\Message;
use core\main\text\TextFormat;
use core\network\NetworkManager;
use pocketmine\player\Player;

class VerifyForm extends CustomForm
{

    private $networkData;
    private $cal;
    private $nulcall;

    public function __construct(Player $player, ?callable $callable, ?callable $nullcallable = null)
    {
        $this->cal = $callable;
        $this->nulcall = $nullcallable;
        parent::__construct($this->getFormResultCallable());
        $this->setTitle(TextFormat::BOLD_GRAY . "Verify");
        $networkData = NetworkManager::getNetworkPlayerDBHandler()->getPlayerObject($player->getXuid());
        $this->networkData = $networkData;
        if ($networkData === null) {
            $this->addLabel(TextFormat::BOLD_GRAY . "An error has occurred!");
            return;
        }
        $this->addInput(TextFormat::GRAY . "Please input your password!");
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?array $data) {
            if ($data === null && $this->nulcall !== null) {
                $call = $this->nulcall;
                $call($player);
                return;
            }
            if ($data === null) {
                $player->sendMessage(Message::PREFIX . "Failed to veryify!");
                return;
            }
            $result = $data[0];
            if (is_string($result)) {
                if (md5($result) === $this->networkData->getPassword()) {
                    $cal = $this->cal;
                    $cal($player);
                    return;
                } else {
                    if ($this->nulcall !== null) {
                        $call = $this->nulcall;
                        $call($player);
                    }
                    $player->sendMessage(Message::PREFIX . "Password does not match!");
                }
            }
        };
    }
}