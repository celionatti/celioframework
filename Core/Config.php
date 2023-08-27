<?php

namespace App\Core;

class Config
{
    private static array $config = [
        'version'               => '1.0.0',
        'default_controller'    => 'Home', // The default home controller
        'default_layout'        => 'default', // Default layout that is used
    ];

    public static function get($key)
    {
        if (array_key_exists($key, $_ENV)) return $_ENV[$key];
        return array_key_exists($key, self::$config) ? self::$config[$key] : NULL;
    }
}