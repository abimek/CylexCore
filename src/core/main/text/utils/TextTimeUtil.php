<?php
declare(strict_types=1);

namespace core\main\text\utils;

class TextTimeUtil
{

    /**
     * @param int $time
     * @param string $number_color
     * @param string $time_color
     * @return string
     */
    public static function secondsToTime(int $time, string $number_color, string $time_color): string
    {
        $days = floor($time / 86400);
        $hours = $hours = floor(($time / 3600) % 24);
        $minutes = floor(($time / 60) % 60);
        $seconds = $time % 60;
        $msg = "";
        if ($days >= 1) $msg .= $number_color . $days . $time_color . "days, ";
        if ($hours >= 1) $msg .= $number_color . $hours . $time_color . "hours, ";
        if ($minutes >= 1) $msg .= $number_color . $minutes . $time_color . "mintues, ";
        if ($seconds >= 1) $msg .= $number_color . $seconds . $time_color . "seconds";
        return $msg;
    }

}