<?php
declare(strict_types=1);

namespace core\forms\formapi\builtin;

use core\forms\formapi\CustomForm;
use core\main\text\message\Message;
use pocketmine\player\Player;

class PlayerSelectForm extends CustomForm
{
    private $cal;

    public function __construct(Player $player, callable $cal)
    {
        $this->cal = $cal;
        parent::__construct($this->getFormResultCallable());
        $this->addInput(Message::ARROW . "Player-Select");
    }


    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?array $data = null) {
            $callable = $this->cal;
            $callable($player, $data);
        };
    }
}


