<?php
declare(strict_types=1);

namespace core\main\commands;

use core\CylexCore;
use pocketmine\command\CommandSender;

abstract class SubCommand
{

    /**
     * @var array
     */
    private $names = [];

    /**
     * @var string
     */
    private $name;

    private $description;

    /**
     * @var BaseCommand
     */
    private $parent;

    public function __construct(string $name, BaseCommand $parent)
    {
        $this->names[] = $name;
        $this->name = $name;
        $this->parent = $parent;
        $this->init();
    }

    public function init()
    {
    }

    /**
     * @return BaseCommand
     */
    public function getParent(): BaseCommand
    {
        return $this->parent;
    }

    /**
     * @return array]
     */
    public function getNames(): array
    {
        return $this->names;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->name;
    }

    /**
     * @return CylexCore
     */
    public function getCore(): CylexCore
    {
        return CylexCore::getInstance();
    }

    /**
     * @param array $aliases
     */
    public function addAliases(array $aliases)
    {
        foreach ($aliases as $alias) {
            $this->names[] = $alias;
        }
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @param array $args
     * @return mixed
     */
    abstract function execute(CommandSender $sender, array $args);

}

