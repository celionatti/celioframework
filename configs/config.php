<?php

use App\Core\Config;

return [
    'database' => [
        'host' => Config::get("host") ?? 'localhost',
        'port' => Config::get("port") ?? 3306,
        'dbname' => Config::get("dbname") ?? 'blog',
        'charset' => 'utf8mb4'
    ],

    'services' => [

    ],
];