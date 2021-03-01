<?php
declare(strict_types=1);

namespace core\main\text\utils;

use core\main\text\TextFormat;

class FormTextUtil
{

    /**
     * @param string $text
     * @param string $header
     * @param string $color
     * @return string
     */
    public static function addHeader(string $text, string $header, string $color)
    {
        return $text . "\n" . TextFormat::BOLD . $color . TextFormat::UNDERLINE . $header . TextFormat::RESET;
    }

    /**
     * @param string $text
     * @param string $point
     * @param string $color
     * @param string $star_color
     * @return string
     */
    public static function addBulletPoint(string $text, string $point, string $color, string $star_color = TextFormat::YELLOW)
    {
        return $text . "\n" . $star_color . "* " . $color . $point;
    }

}