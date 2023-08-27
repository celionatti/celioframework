<?php

/** @var TYPE_NAME $app */

use App\controllers\SiteController;
use App\controllers\AdminController;


$app->router->get('/', [SiteController::class, 'index']);
$app->router->post('/', [SiteController::class, 'index']);
$app->router->get('/users/{id}', [SiteController::class, 'users']);
$app->router->get('/admin/{id}', 'controllers/admin.php');

// // Admin
// $app->router->get('/admin', [AdminController::class, 'index'])->only('admin');