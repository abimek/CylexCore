<?php
declare(strict_types=1);

namespace core\forms\formapi\builtin;

use core\forms\formapi\CustomForm;
use core\main\text\message\Message;
use pocketmine\player\Player;
use pocketmine\Server;

class OnlinePlayerSelectForm extends CustomForm
{
    private $report;
    private $options = [];

    public function __construct(Player $player)
    {
        parent::__construct($this->getFormResultCallable());
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            $this->options[] = $player->getName();
        }
        $this->addDropdown(Message::ARROW . "Online-Players", $this->options);
    }


    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?array $data = null) {
            $callabel = $this->getCall();
            $callabel($player, $data, $this->options);
        };
    }

    public function getCall(): callable
    {

    }
}


