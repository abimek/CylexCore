<?php
declare(strict_types=1);

namespace core\admin\messages;

use core\main\base\BaseMessageType;
use core\main\text\message\Message;
use core\main\text\TextFormat;

class AdminMessageType extends BaseMessageType
{

    public const IDENTIFIER = "AdminMessageType";

    public const TYPE_ONLY_ADMIN = "onlyadmins";

    public static function getIdentifier()
    {
        return self::IDENTIFIER;
    }

    public function init()
    {
        $this->addMessage(self::TYPE_ONLY_ADMIN, Message::PREFIX . "Sadly :( you do not have permission to use the command (" . TextFormat::LIGHT_PURPLE . "{command}" . TextFormat::GRAY . ")");
    }

    public function getId()
    {
        return self::IDENTIFIER;
    }
}