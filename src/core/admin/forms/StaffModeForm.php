<?php
declare(strict_types=1);

namespace core\admin\forms;

use core\admin\handlers\StaffModeHandler;
use core\forms\formapi\ModalForm;
use core\main\text\TextFormat;
use core\players\PlayerManager;
use pocketmine\player\Player;

class StaffModeForm extends ModalForm
{

    public function __construct()
    {
        parent::__construct($this->getFormResultCallable());
        $this->setTitle(TextFormat::GRAY . "Staff-Mode");
        $this->setContent(TextFormat::GRAY . "Would you like to toggle " . TextFormat::LIGHT_PURPLE . "staff-mode?");
        $this->setButton1(TextFormat::LIGHT_PURPLE . "YES");
        $this->setButton2(TextFormat::LIGHT_PURPLE . "NO");
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?bool $data) {
            if ($data === null) {
                return;
            }
            if ($data === true) {
                StaffModeHandler::toggleStaffMode(PlayerManager::getSession($player->getXuid()));
                return;
            }
        };
    }
}
