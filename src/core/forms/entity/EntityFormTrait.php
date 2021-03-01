<?php

declare(strict_types=1);

namespace core\forms\entity;

use pocketmine\entity\Entity;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\player\Player;

trait EntityFormTrait
{
    public $form_listeners = [];
    public $entity = null;
    public $buttons = [];
    private $data = ["title" => "", "content" => ""];
    private $entity_damageable = false;


    public function initEntityForm(string $title, bool $entity_damageable = false)
    {
        $this->getNetworkProperties()->setByte(EntityMetadataProperties::HAS_NPC_COMPONENT, 1);
        $this->setTitle($title);
        $this->setEntityDamageable($entity_damageable);
    }


    /**
     * @param string $title
     */
    final public function setTitle(string $title): void
    {
        $this->data["title"] = $title;
        $this->setNameTag($title);
    }

    /**
     * @return string
     */
    final public function getTitle(): string
    {
        return $this->data["title"] ?? "";
    }

    /**
     * @param string $content
     */
    final public function setContent(string $content): void
    {
        $this->data["content"] = $content;
        $this->getNetworkProperties()->setString(EntityMetadataProperties::INTERACTIVE_TAG, $this->getContent());
    }

    /**
     * @return string
     */
    final public function getContent(): string
    {
        return $this->data["content"];
    }

    /**
     * @return bool
     */
    final public function isEntityDamageable(): bool
    {
        return $this->entity_damageable;
    }

    /**
     * @param bool $value
     */
    final public function setEntityDamageable(bool $value): void
    {
        $this->entity_damageable = $value;
    }

    /**
     * @return Entity|null
     */
    final public function getEntity(): ?Entity
    {
        return $this->entity;
    }


    /**
     * @param Button $button
     * @param callable|null $callable
     */
    final public function addButton(Button $button, ?callable $callable = null): void
    {
        $this->buttons[] = $button;
        if ($callable === null) {
            return;
        }
        $this->form_listeners[array_key_last($this->buttons)] = $callable;
        $this->getNetworkProperties()->setString(EntityMetadataProperties::NPC_ACTIONS, json_encode(array_map(function ($button) {
            return $button->data;
        }, $this->buttons), JSON_UNESCAPED_UNICODE));
    }

    /**
     * @param Player $player
     * @param int|null $data
     */
    final public function handleResponse(Player $player, ?int $data): void
    {
        if ($data === null) {
            $this->onClose($player);
        }
        if (isset($this->form_listeners[$data])) {
            ($this->form_listeners[$data])($player, $data);
            return;
        }
        if (isset($this->buttons[$data])) {
            $this->onButtonInteract($player, $this->buttons[$data], $data);
        }
    }

    /**
     * @param Player $player
     */
    public function onClose(Player $player)
    {
    }

    /**
     * @param Player $player
     * @param Button $button
     * @param int $data
     */
    public function onButtonInteract(Player $player, Button $button, int $data)
    {
    }

    /**
     * @param Player $player
     */
    public function onOpen(Player $player)
    {

    }

    /**
     * @return array|mixed
     */
    final public function jsonSerializeActions()
    {
        return array_map(function ($button) {
            return $button->data;
        }, $this->buttons);
    }

}