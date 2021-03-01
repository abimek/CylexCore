<?php

namespace core\forms\entity\listener;

use core\forms\entity\EntityFormTrait;
use core\main\base\BaseListener;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\NpcRequestPacket;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\Server;

class EntityFormListener extends BaseListener
{
    public static $skin = null;
    private $npc;

    /**
     * @priority LOWEST
     * @param DataPacketReceiveEvent $event
     */
    public function DataPacketReceive(DataPacketReceiveEvent $event)
    {
        $pk = $event->getPacket();
        $player = $event->getOrigin()->getPlayer();
        if ($pk instanceof NpcRequestPacket) {
            if (($entity = Server::getInstance()->getWorldManager()->findEntity($pk->entityRuntimeId)) === null) {
                return;
            }
            if (isset(class_uses($entity)[EntityFormTrait::class])) {
                switch ($pk->requestType) {
                    case NpcRequestPacket::REQUEST_EXECUTE_ACTION:
                        $this->npc[$player->getName()] = $pk->actionType;
                        break;
                    case NpcRequestPacket::REQUEST_EXECUTE_CLOSING_COMMANDS:
                        if (isset($this->npc[$player->getName()])) {
                            $response = $this->npc[$player->getName()];
                            unset($this->npc[$player->getName()]);
                            if (isset(class_uses($entity)[EntityFormTrait::class])) {
                                $entity->handleResponse($player, $response);
                            }
                        }
                        break;
                }
            }
        }
        if ($pk instanceof InventoryTransactionPacket) {
            $trdata = $pk->trData;
            if ($trdata instanceof UseItemOnEntityTransactionData && $trdata->getActionType() === UseItemOnEntityTransactionData::ACTION_INTERACT) {

                $entityid = $trdata->getEntityRuntimeId();
                if (($entity = Server::getInstance()->getWorldManager()->findEntity($entityid)) === null) {
                    return;
                }
                if (isset(class_uses($entity)[EntityFormTrait::class])) {
                    $entity->onOpen($player);
                }
            }
        }

    }

    /**
     * @param EntityDamageEvent $event
     */
    public function onDamage(EntityDamageEvent $event)
    {
        $entity = $event->getEntity();
        if (isset(class_uses($entity)[EntityFormTrait::class])) {
            if ($entity->isEntityDamageable() === true) {
                return;
            }
            $event->cancel();
        }
    }

    protected function init(): void
    {
        // TODO: Implement init() method.
    }
}
