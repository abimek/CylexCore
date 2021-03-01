<?php
declare(strict_types=1);

namespace core\admin\listeners;

use core\admin\handlers\MuteHandler;
use core\main\base\BaseListener;
use core\main\text\message\Message;
use core\main\text\TextFormat;
use core\players\PlayerManager;
use pocketmine\event\player\PlayerChatEvent;

class PlayerChatListener extends BaseListener
{


    public function onChat(PlayerChatEvent $event)
    {
        $xuid = $event->getPlayer()->getXuid();
        $session = PlayerManager::getSession($xuid);
        if ($session === null) {
            return;
        }
        if ($session->getObject()->getBanData()->isMuted()) {
            $event->setFormat("");
            $event->getPlayer()->sendMessage(Message::PREFIX . "You are currently " . TextFormat::LIGHT_PURPLE . "muted " . TextFormat::GRAY . "until " . TextFormat::RED . date("F j, Y, g:i a", $session->getObject()->getBanData()->getMuteDuration()));
        }
        if ($session->getObject()->getBanData()->getMuteDuration() < time()) {
            MuteHandler::unMutePlayer($session->getObject());
        }
    }

    protected function init(): void
    {

    }
}