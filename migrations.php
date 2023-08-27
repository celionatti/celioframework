<?php

use Dotenv\Dotenv;

/**
 * Laragon Migrations File.
 */


require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

require __DIR__ . '/Core/Function.php';


$migration = new \App\Core\Database\Migration();

$migration->applyMigrations();