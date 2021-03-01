<?php
declare(strict_types=1);

namespace core\admin\forms\mute;

use core\forms\formapi\builtin\OnlinePlayerSelectForm;
use core\main\text\TextFormat;
use core\players\objects\PlayerObject;
use core\players\PlayerManager;
use pocketmine\player\Player;

class MuteSelectForm extends OnlinePlayerSelectForm
{

    public function __construct(Player $player)
    {
        parent::__construct($player);
        $this->setTitle(TextFormat::BOLD_LIGHT_PURPLE . "Mute-Selector");
    }

    public function getCall(): callable
    {
        return function (Player $player1, ?array $data = null, array $options) {
            if ($data === null) {
                return;
            }
            $result = $data[0];
            $player = $options[$result];

            PlayerManager::getDatabaseHandler()->getPlayerObjectByUsername($player, function ($object) use ($player1) {
                if ($object instanceof PlayerObject) {
                    $form = new MuteForm($player1, $object);
                    $player1->sendForm($form);
                }
            });
        };
    }
}