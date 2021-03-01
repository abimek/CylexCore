<?php
declare(strict_types=1);

namespace core\main\managers;

use core\CylexCore;
use core\main\base\BaseListener;

abstract class Manager
{

    public function __construct()
    {
        $this->init();
    }

    abstract protected function init(): void;

    public function disable()
    {
        $this->close();
    }

    abstract protected function close(): void;

    protected function registerListener(BaseListener $baseListener): BaseListener
    {
        $this->getCore()->getServer()->getPluginManager()->registerEvents($baseListener, $this->getCore());
        return $baseListener;
    }

    public function getCore(): CylexCore
    {
        return CylexCore::getInstance();
    }

}