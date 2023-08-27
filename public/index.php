<?php

use Dotenv\Dotenv;
use App\Core\Application;

/**
 * Laragon Main Entry (Index)
 */


require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

require dirname(__DIR__) . '/Core/Function.php';

$app = new Application();

require base_path('routes/web.php');

$app->run();