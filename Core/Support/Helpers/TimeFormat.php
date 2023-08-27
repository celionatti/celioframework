<?php

namespace App\Core\Support\Helpers;

use App\Core\Config;


class TimeFormat
{
    /**
     * TimeInAgo Function
     *
     * This function makes Time Formats More Readable;
     * And Easy to Understand;
     *
     * @param $timeStamp
     * @return string|void
     */
    public static function TimeInAgo($timeStamp)
    {
        date_default_timezone_set(Config::get('time_zone'));

        $timestamp = strtotime($timeStamp) ? strtotime($timeStamp) : $timeStamp;

        $time = time() - $timestamp;

        switch ($time) {
            // Seconds
            case $time <= 60:
                return 'Just Now!';
            // Minutes
            case $time >= 60 && $time < 3600;
                return (round($time / 60) == 1) ? 'a month ago' : round($time / 60) . 'mins ago';
            // Hours
            case $time >= 3600 && $time < 86400;
                return (round($time / 3600) == 1) ? 'an hour ago' : round($time / 3600) . 'hours ago';
            // Days
            case $time >= 86400 && $time < 604800;
                return (round($time / 86400) == 1) ? 'a day ago' : round($time / 86400) . 'days ago';
            // Weeks
            case $time >= 604800 && $time < 2600640;
                return (round($time / 604800) == 1) ? 'a week ago' : round($time / 604800) . 'Weeks ago';
            // Months
            case $time >= 2600640 && $time < 31207680;
                return (round($time / 604800) == 1) ? 'a month ago' : round($time / 2600640) . 'Months ago';
            // Years
            case $time >= 31207680;
                return (round($time / 31207680) == 1) ? 'a year ago' : round($time / 31207680) . 'years ago';
        }
    }

    public static function StringTime($timeStamp): string
    {
        date_default_timezone_set(Config::get('time_zone'));

        $time_ago = strtotime($timeStamp);
        $current_time = time();
        $time_difference = $current_time - $time_ago;

        $seconds = $time_difference;
        $minutes = round($seconds / 60); //value 60 is seconds
        $hours = round($seconds / 3600); //value 3600 is minutes * 60 seconds
        $days = round($seconds / 86400); //86400 = 24 * 60 * 60
        $weeks = round($seconds / 604800); //7 * 24 * 60 * 60
        $months = round($seconds / 2629440); // ((365+365+365+365+366)/5/12)* 24*60*60
        $years = round($seconds / 31553280); // (365+365+365+365+366)/5 * 24*60*60

        if ($seconds <= 60) {
            return "Just Now";
        } elseif ($minutes <= 60) {
            if ($minutes == 1) {
                return "one minute ago";
            } else {
                return "$minutes minutes ago";
            }
        } elseif ($hours <= 24) {
            if ($hours == 1) {
                return "an hour ago";
            } else {
                return "$hours hrs ago";
            }
        } elseif ($days <= 7) {
            if ($days == 1) {
                return "yesterday";
            } else {
                return "$days days ago";
            }
        } elseif ($weeks <= 4.3) //4.3 == 52/12
        {
            if ($weeks == 1) {
                return "a week ago";
            } else {
                return "$weeks weeks ago";
            }
        } elseif ($months <= 12) {
            if ($months == 1) {
                return "a month ago";
            } else {
                return "$months months ago";
            }
        } else {
            if ($years == 1) {
                return "one year ago";
            } else {
                return "$years years ago";
            }
        }
    }

    public static function DateOne($timeStamp): string
    {
        date_default_timezone_set(Config::get('time_zone'));

        return date("M j, Y ~ g:i a", strtotime($timeStamp));
    }

    public static function DateTwo($timeStamp): string
    {
        date_default_timezone_set(Config::get('time_zone'));

        return date("j M Y", strtotime($timeStamp));
    }

    public static function BlogDate($timeStamp): string
    {
        date_default_timezone_set(Config::get('time_zone'));

        return date("g:i a T. F j, Y", strtotime($timeStamp));
    }
}