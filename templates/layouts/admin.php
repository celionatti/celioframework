<?php

use App\Core\Config;
use App\Core\Support\FlashMessage;


/** @var $this \App\Core\View */

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?= assets_path('img/favicon.png') ?>" />
    <link rel="apple-touch-icon" href="<?= assets_path('img/favicon.png') ?>" />
    <title>
        <?= $this->getTitle() ?>
    </title>
    <link type="text/css" rel="stylesheet" href="<?= assets_path('bootstrap/css/bootstrap-icons.css'); ?>">
    <link type="text/css" rel="stylesheet" href="<?= assets_path('bootstrap/css/bootstrap.min.css'); ?>">
    <link type="text/css" rel="stylesheet" href="<?= assets_path('css/dashboard.css'); ?>">
    <?php $this->content('header') ?>
    <style>
    .nav-scroller {
        position: relative;
        z-index: 2;
        height: 2.75rem;
        overflow-y: hidden;
    }

    .nav-scroller .nav {
        display: flex;
        flex-wrap: nowrap;
        padding-bottom: 1rem;
        margin-top: -1px;
        overflow-x: auto;
        text-align: center;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
    }
    </style>
</head>

<body style="background: #eee">
    <?php $this->partial('admin-header') ?>
    <div class="container-fluid">
        <?= FlashMessage::bootstrap_alert(); ?>
        <div class="row">
            <?php $this->partial('admin-sidebar') ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <?php $this->partial('admin-crumbs') ?>
                <?php include('../templates/partials/breadcrumbs.php') ?>
                <?php $this->content('content'); ?>
            </main>
        </div>
    </div>

    <script src="<?= assets_path('js/jquery-3.6.3.min.js'); ?>"></script>
    <script src="<?= assets_path('bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>

    <?php $this->content('script') ?>
</body>

</html>