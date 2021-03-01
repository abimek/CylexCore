<?php
declare(strict_types=1);

namespace core\admin\forms\player_info;

use core\forms\formapi\CustomForm;
use core\main\data\formatter\BooleanFormatter;
use core\main\text\message\Message;
use core\players\objects\PlayerObject;
use core\players\PlayerManager;
use core\ranks\levels\StaffRankLevels;
use core\ranks\RankManager;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class PlayerInfoForm extends CustomForm
{

    use BooleanFormatter;

    public function __construct(Player $player, PlayerObject $object)
    {
        $session = PlayerManager::getSession($player->getXuid());
        if ($session === null) {
            return;
        }
        $rank_id = $session->getRankIdentifier();
        $rank = RankManager::getRank($rank_id);
        if ($rank === null) {
            return;
        }
        parent::__construct($this->getFormResultCallable());
        if ($rank->getLevel() >= StaffRankLevels::MOD) {
            $this->setTitle(TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "PlayerInfo-Se");
            $ban_count = $object->getBanCount();
            $is_banned = $this->boolToString($object->getBanData()->isBanned());
            $is_IpBanned = $this->boolToString($object->getBanData()->isIpBanned());
            $aliases = "";
            $this->addLabel(Message::ARROW . "Ban-Count: " . TextFormat::RED . "$ban_count");
            $this->addLabel(Message::ARROW . "Banned: " . TextFormat::RED . "$is_banned");
            $this->addLabel(Message::ARROW . "IpBanned: " . TextFormat::RED . "$is_IpBanned");
            foreach ($object->getBanData()->getAliases() as $alias) {
                PlayerManager::getDatabaseHandler()->getPlayerObjectByUsername($alias, function ($alias_object) use ($alias, &$aliases) {
                    $banned = $alias_object->getBanData()->isBanned();
                    $aliases = TextFormat::GRAY . $alias . " ";
                    $ipbanned = $alias_object->getBanData()->isIpBanned();
                    if ($ipbanned) {
                        $aliases .= TextFormat::RED . "(IPBANNED), ";
                        return;
                    } else {
                        if ($banned) {
                            $aliases .= TextFormat::RED . "(BANNED), ";
                        } else {
                            $aliases .= TextFormat::GREEN . "(NOT_BANNED), ";
                        }
                    }
                });
            }
            if ($aliases === "") {
                $this->addLabel(Message::ARROW . "No-Aliases");
            } else {
                $this->addLabel(Message::ARROW . TextFormat::GOLD . "Aliases: " . TextFormat::RESET . TextFormat::GRAY . $aliases);
            }
            $this->addLabel(Message::ARROW . TextFormat::GOLD . "Rank: " . TextFormat::LIGHT_PURPLE . $object->getRank());
        }
    }


    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?array $data = null) {
        };
    }
}