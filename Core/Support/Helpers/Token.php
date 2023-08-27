<?php

namespace App\Core\Support\Helpers;

use Exception;

class Token
{
    /**
     * @throws Exception
     */
    public static function rand_str(): string
    {
        $characters = '0123456789-=+{}[]:;@#~.?/&gt;,&lt;|\!"£$%^&amp;*()abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomStr = '';
        for ($i = 0; $i < random_int(50, 100); $i++) {
            $randomStr .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomStr;
    }

    public static function generateOTP($length): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function strToUrl($url): array|string|null
    {
        $url = str_replace("'", "", $url);
        $url = preg_replace('~[^\\pL0-9_]+~u', '_', $url);
        $url = trim($url, "_");
        $url = iconv("utf-8", "us-ascii//TRANSLIT", $url);
        $url = strtolower($url);
        return preg_replace('~[^-a-z0-9_]+~', '', $url);
    }

    public static function randomString($length): string
    {
        $array = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');

        $text = "";

        for ($i = 0; $i < $length; $i++) {
            $random = rand(0, 61);
            $text .= $array[$random];
        }
        return $text;
    }

    public static function randomPassword(): string
    {
        $alphabet = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    public static function TransactID($length, $prefix = ''): string
    {
        $array = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);

        $text = "";

        for ($i = 0; $i < $length; $i++) {
            $random = rand(0, 9);
            $text .= $array[$random];
        }
        return $prefix . ':' . strtolower($text);
    }

    public static function RandomNumber($length): string
    {
        $array = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);

        $text = "";

        for ($i = 0; $i < $length; $i++) {
            $random = rand(0, 9);
            $text .= $array[$random];
        }
        return $text;
    }
}