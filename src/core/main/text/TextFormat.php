<?php
declare(strict_types=1);

namespace core\main\text;

final class TextFormat extends \pocketmine\utils\TextFormat
{

    public const WARNING = self::BOLD_GRAY . "(" . self::RESET_RED . "!" . self::BOLD_GRAY . ")" . self::RESET_GRAY . " ";
    public const ADVISE = self::BOLD_GRAY . "(" . self::RESET_GREEN . "!" . self::BOLD_GRAY . ")" . self::RESET_GRAY . " ";

    public const BOLD_BLACK = self::BOLD . self::BLACK;
    public const BOLD_DARK_BLUE = self::BOLD . self::DARK_BLUE;
    public const BOLD_DARK_GREEN = self::BOLD . self::DARK_GREEN;
    public const BOLD_DARK_AQUA = self::BOLD . self::DARK_AQUA;
    public const BOLD_DARK_RED = self::BOLD . self::DARK_RED;
    public const BOLD_DARK_PURPLE = self::BOLD . self::DARK_PURPLE;
    public const BOLD_GOLD = self::BOLD . self::GOLD;
    public const BOLD_GRAY = self::BOLD . self::GRAY;
    public const BOLD_DARK_GRAY = self::BOLD . self::DARK_GRAY;
    public const BOLD_BLUE = self::BOLD . self::BLUE;
    public const BOLD_GREEN = self::BOLD . self::GREEN;
    public const BOLD_AQUA = self::BOLD . self::AQUA;
    public const BOLD_RED = self::BOLD . self::RED;
    public const BOLD_LIGHT_PURPLE = self::BOLD . self::LIGHT_PURPLE;
    public const BOLD_YELLOW = self::BOLD . self::YELLOW;
    public const BOLD_WHITE = self::BOLD . self::WHITE;

    public const RESET_BLACK = self::RESET . self::BLACK;
    public const RESET_DARK_BLUE = self::RESET . self::DARK_BLUE;
    public const RESET_DARK_GREEN = self::RESET . self::DARK_GREEN;
    public const RESET_DARK_AQUA = self::RESET . self::DARK_AQUA;
    public const RESET_DARK_RED = self::RESET . self::DARK_RED;
    public const RESET_DARK_PURPLE = self::RESET . self::DARK_PURPLE;
    public const RESET_GOLD = self::RESET . self::GOLD;
    public const RESET_GRAY = self::RESET . self::GRAY;
    public const RESET_DARK_GRAY = self::RESET . self::DARK_GRAY;
    public const RESET_BLUE = self::RESET . self::BLUE;
    public const RESET_GREEN = self::RESET . self::GREEN;
    public const RESET_AQUA = self::RESET . self::AQUA;
    public const RESET_RED = self::RESET . self::RED;
    public const RESET_LIGHT_PURPLE = self::RESET . self::LIGHT_PURPLE;
    public const RESET_YELLOW = self::RESET . self::YELLOW;
    public const RESET_WHITE = self::RESET . self::WHITE;

    public const BOLD_OBFUSCATED = self::BOLD . self::ESCAPE . "k";
    public const BOLD_STRIKETHROUGH = self::BOLD . self::ESCAPE . "m";
    public const BOLD_UNDERLINE = self::BOLD . self::ESCAPE . "n";
    public const BOLD_ITALIC = self::BOLD . self::ESCAPE . "o";
    public const BOLD_RESET = self::BOLD . self::ESCAPE . "r";
}