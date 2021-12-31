<?php
declare(strict_types=1);

namespace core\network\forms\account_forms\mail;

use core\forms\formapi\CustomForm;
use core\main\text\message\Message;
use core\main\text\TextFormat;
use core\main\text\utils\TextUtil;
use core\network\NetworkManager;
use core\network\objects\Mail;
use core\network\objects\NetworkPlayer;
use core\players\objects\PlayerObject;
use core\players\PlayerManager;
use pocketmine\player\Player;
use Ramsey\Uuid\Nonstandard\Uuid;

class MailComposeForm extends CustomForm
{

    private $networkData;

    public function __construct(Player $player)
    {
        parent::__construct($this->getFormResultCallable());
        $this->setTitle(TextFormat::BOLD_GRAY . "Compose");
        $networkData = NetworkManager::getNetworkPlayerDBHandler()->getPlayerObject($player->getXuid());
        $this->networkData = $networkData;
        if ($networkData === null) {
            $this->addLabel("an error occurred");
            return;
        }
        $this->addInput(TextFormat::GRAY . "Player: ", "");
        $this->addInput(TextFormat::RED . "Title (15 character max)");
        $this->addLabel(TextFormat::RED . "Date: " . TextFormat::GRAY . date("F j, Y, g:i a", time()));
        $this->addLabel(TextFormat::RED . "Sender: " . $player->getName());
        $this->addInput(TextFormat::GRAY . "Message: ", "");
    }

    public function getFormResultCallable(): callable
    {
        return function (Player $player, ?array $data) {
            if ($data === null) {
                return;
            }
            if (!is_string($data[0])) {
                return;
            }
            if (strlen($data[1]) > 15) {
                $player->sendMessage(Message::PREFIX . "Your title was over 15 characters!");
                return;
            }
            if ($data[4] === "") {
                $player->sendMessage(Message::PREFIX . "Unable to send blank message!");
                return;
            }
            if (TextUtil::cussFilter($data[4]) === true || TextUtil::cussFilter($data[1]) === true) {
                $player->sendMessage(Message::PREFIX . "Unable to send a message that contains cuss-words!");
                return;
            }
            PlayerManager::getDatabaseHandler()->getPlayerObjectByUsername($data[0], function ($object) use ($player, $data) {
                if ($object instanceof PlayerObject) {
                    $name = $player->getName();
                    $xuid = $object->getXuid();
                    NetworkManager::getNetworkPlayerDBHandler()->loadAccountAndCallable($xuid, function (NetworkPlayer $nplayer) use ($data, $name) {
                        $mail = new Mail(Uuid::uuid4()->toString(), time(), $data[1], $data[4], $name);
                        $nplayer->addMail($mail->encodeData());
                    });
                    $player->sendMessage(Message::PREFIX . "Successfully sent mail to " . TextFormat::LIGHT_PURPLE . $data[0]);
                    return;
                }
                $player->sendMessage(Message::PREFIX . "The player (" . TextFormat::LIGHT_PURPLE . $data[0] . TextFormat::LIGHT_PURPLE . ") seems to not exist!");

            });
        };
    }
}
