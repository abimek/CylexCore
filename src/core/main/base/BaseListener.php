<?php
declare(strict_types=1);

namespace core\main\base;

use pocketmine\event\Listener;

abstract class BaseListener implements Listener
{
    public function __construct()
    {
        $this->init();
    }

    abstract protected function init(): void;
}