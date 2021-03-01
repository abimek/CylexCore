<?php
declare(strict_types=1);

namespace core\admin\forms\warn;

use core\forms\formapi\builtin\OnlinePlayerSelectForm;
use core\main\text\TextFormat;
use pocketmine\player\Player;

class WarnSelectForm extends OnlinePlayerSelectForm
{

    public function __construct(Player $player)
    {
        parent::__construct($player);
        $this->setTitle(TextFormat::BOLD_LIGHT_PURPLE . "Warn-Selector");
    }

    public function getCall(): callable
    {
        return function (Player $player1, ?array $data = null, array $options) {
            if ($data === null) {
                return;
            }
            $result = $data[0];
            $player = $options[$result];
            $form = new WarnForm($player1, $player);
            $player1->sendForm($form);
        };
    }
}