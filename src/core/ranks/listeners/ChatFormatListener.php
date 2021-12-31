<?php
declare(strict_types=1);

namespace core\ranks\listeners;

use core\main\base\BaseListener;
use core\main\text\message\Message;
use core\main\text\TextFormat;
use core\main\text\utils\TextUtil;
use core\players\PlayerManager;
use core\ranks\RankManager;
use pocketmine\event\player\PlayerChatEvent;

class ChatFormatListener extends BaseListener
{
    private $spam = [];

    /**
     * @param PlayerChatEvent $event
     */
    public function onChat(PlayerChatEvent $event)
    {
        $player = $event->getPlayer();
        $xuid = $player->getXuid();
        if (isset($this->spam[$xuid])) {
            if ($this->spam[$xuid] > time()) {
                $time = $this->spam[$xuid] - time();
                $player->sendMessage(Message::PREFIX . "You can send another message in " . TextFormat::RED . $time . TextFormat::GRAY . "s");
                $event->cancel();
                return;
            } else {
                $this->spam[$xuid] = time() + 2;
            }
        } else {
            $this->spam[$xuid] = time() + 2;
        }
        $callables = RankManager::getEditCallables();
        $session = PlayerManager::getSession($player->getXuid());
        $rankIdentifier = $session->getRankIdentifier();
        $rank = RankManager::getRank($rankIdentifier);
        if ($rank === null) {
            return;
        }
        $filter = TextUtil::chatFilter($event->getMessage());

        $format = $rank->getChatFormat();
        $format = TextUtil::replaceText($format, ["{name}" => $player->getName()]);
        if ($filter !== null){
            $format = TextUtil::replaceText($format, ["{msg}" => $filter]);
        }
        if ($filter === null) {
            $event->cancel();
            $player->sendMessage("" . TextFormat::GRAY . "Unable to send message.");
            return;
        }
        foreach ($callables as $callable) {
            $callable($format, $player->getXuid(), $event);
        }
        $event->setFormat($format);
    }

    protected function init(): void
    {

    }
}