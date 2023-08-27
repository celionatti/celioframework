<?php

namespace App\Core\Support\Helpers;

class StringFormat
{
    /**
     * Excerpt Function
     *
     * This function helps short Text Content;
     * Accept Two Params
     * @param string $text for the variable to short
     * @param int|string $length for number of length
     *
     * @return string
     */
    public static function Excerpt(string $text, int|string $length = 250): string
    {
        return substr($text, 0, $length) . '...';
    }

    public static function StrToLower($text): string
    {
        return strtolower($text);
    }

    public static function StrToUpper($text): string
    {
        return strtoupper($text);
    }
}