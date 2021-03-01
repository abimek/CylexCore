<?php
declare(strict_types=1);

namespace core\main\base;

abstract class BaseMessageType
{

    protected $message;

    public function __construct()
    {
        $this->init();
    }

    abstract function init();

    abstract static function getIdentifier();

    public function addMessage(string $id, string $message)
    {
        $this->message[$id] = $message;
    }

    public function getMessage(string $id): ?string
    {
        if (isset($this->message[$id])) {
            return $this->message[$id];
        }
        return null;
    }

    abstract function getId();

}


