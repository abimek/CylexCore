<?php
declare(strict_types=1);

namespace core\main\text\utils;

class TextUtil
{

    public const CHAT_FILTERED_WORDS = [
        "niga",
        "nigga",
        "nigger",
        "pussy",
        "rape",
        "rapest",
        "raper",
        "motherfucker",
        "fucker",
        "fuck",
        "dick",
        "whore",
        "shit",
        "boobs",
        "boob",
        "nazi",
        "natzi",
        "wank",
        "bitch",
        "tit",
        "titties",
        "tits",
        "§"
    ];

    /**
     * @param string $msg
     * @param array $replaceData
     * @return string
     */
    public static function replaceText(string $msg, array $replaceData): ?string
    {
        if (empty($replaceData)) {
            return $msg;
        }
        $array_replaced_text = [];
        $array_replace_with = [];
        foreach ($replaceData as $replaced_text => $replace_with) {
            $array_replaced_text[] = $replaced_text;
            $array_replace_with[] = $replace_with;
        }
        return str_replace($array_replaced_text, $replace_with, $msg);
    }

    public static function chatFilter(string $message): ?string
    {
        $msg = self::boldSearch($message);
        $cussFilter = self::cussFilter($message);
        if ($cussFilter === true || self::colorSearch($msg)) {
            return null;
        }
        return $msg;
    }

    public static function boldSearch(string $message): string
    {
        $array = explode(" ", $message);
        foreach ($array as $str) {
            if (strlen(preg_replace('![^A-Z]+!', '', $str)) > 1) {
                return strtolower($message);
            }
        }
        return $message;
    }

    public static function cussFilter(string $message): bool
    {
        $cuss = false;
        foreach (self::CHAT_FILTERED_WORDS as $word) {
            $word = " " . $word . " ";
            if (strpos($message, $word) !== false) {
                $cuss = true;
                break;
            }
        }
        return $cuss;
    }

    public static function colorSearch(string $message): bool
    {
        if (strpos($message, "§") !== false) {
            return true;
        }
        return false;
    }

}