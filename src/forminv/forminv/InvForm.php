<?php
declare(strict_types=1);

namespace forminv\forminv;

use core\forms\formapi\SimpleForm;
use core\players\session\PlayerSession;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;

class InvForm
{

    private $guidata = [];
    private $formdata = [];

    private $name;
    private $type;

    private $sound = false;
    private $close = false;
    private $content = "";
    private $setname = false;

    public function __construct(string $name, string $type, $sound = false, $close = true, $setname = false)
    {
        $this->name = $name;
        $this->type = $type;
        $this->sound = $sound;
        $this->close = $close;
        $this->setname = $setname;
    }

    public function setContent(string $content)
    {
        $this->content = $content;
    }

    public function addButton(array $slots, Item $item, string $name = null, callable $callable = null, string $texture = null)
    {
        $this->guidata[] = [$slots, $item, $callable, $name];
        if ($name !== null) {
            $this->formdata[] = [$name, $texture, $callable];
        }
    }

    public function send(PlayerSession $session)
    {
        if ($session !== null) {
            if ($session->wantsGUI()) {
                $this->sendGUI($session);
            } else {
                $this->sendForm($session);
            }
        }
    }

    public function sendGUI(PlayerSession $session)
    {
        $inv = InvMenu::create($this->type);
        $inv->setName($this->getName());
        $item_id_meta_map = [];
        foreach ($this->guidata as $key => $guidatum) {
            $slots = $guidatum[0];
            $item = $guidatum[1];
            $callable = $guidatum[2];
            $name = $guidatum[3];
            if ($callable !== null) {
                $item_id_meta_map[$item->getId() | $item->getMeta()] = $callable;
            }
            foreach ($slots as $slot) {
                if ($this->setname === true) {
                    $item->setCustomName($name);
                }
                $inv->getInventory()->setItem($slot, $item);
            }
        }
        $inv->setListener(function (InvMenuTransaction $transaction) use ($item_id_meta_map, $inv) : InvMenuTransactionResult {
            $clicked = $transaction->getItemClicked();
            $player = $transaction->getPlayer();
            $player->getCursorInventory()->setItem(0, ItemFactory::getInstance()->get(0));
            if (isset($item_id_meta_map[$clicked->getId() | $clicked->getMeta()])) {
                $callable = $item_id_meta_map[$clicked->getId() | $clicked->getMeta()];
                $callable($player);
                if ($this->close) {
                    $inv->onClose($player);
                }
            }
            return $transaction->discard();
        });
        $inv->send($session->getPlayer());
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function sendForm(PlayerSession $session)
    {
        $form = new SimpleForm(function (Player $player, int $data = null) {
            if ($data === null) {
                return;
            }
            if (isset($this->formdata[$data])) {
                $data = $this->formdata[$data];
                $callable = $data[2];
                $callable($player);
            }
        });
        $form->setTitle($this->getName());
        $form->setContent($this->content);
        foreach ($this->formdata as $formdatum) {
            $name = $formdatum[0];
            $texture = $formdatum[1];
            if ($texture !== null) {
                $form->addButton($name, SimpleForm::IMAGE_TYPE_PATH, $texture);
            } else {
                $form->addButton($name);
            }
        }
        $session->getPlayer()->sendForm($form);
    }

}