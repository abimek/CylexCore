<?php
declare(strict_types=1);

namespace core\main\text\message;

use core\main\base\BaseMessageType;
use core\main\text\TextFormat;
use core\main\text\utils\TextUtil;

final class Message
{

    public const PREFIX = TextFormat::BOLD_DARK_GRAY . "[" . TextFormat::RED . "!" . TextFormat::BOLD_DARK_GRAY . "] " . TextFormat::RESET_GRAY;
    public const ARROW = TextFormat::BOLD_LIGHT_PURPLE . "> " . TextFormat::RESET_GRAY;


    private static $types = [];


    public static function getMessage($identifier, string $message, ?array $replace = null)
    {
        if (!isset(self::$types[$identifier])) {
            throw new MessageException("A Message Type with the identifier ($identifier) has has not been registered!");
        }
        $msg = self::$types[$identifier]->getMessage($message);
        if ($msg === null) {
            throw new MessageException("The message $message has has not been found in ($identifier)!");
        }
        if ($replace === null) {
            return $msg;
        } else {
            return TextUtil::replaceText($msg, $replace);
        }
    }


    public static function registerType(BaseMessageType $type)
    {
        $identifier = $type->getId();
        if (isset(self::$types[$identifier])) {
            throw new MessageException("A Message Type with the identifier ($identifier) has already been registered!");
        }
        self::$types[$identifier] = $type;
    }
}