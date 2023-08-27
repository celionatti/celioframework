<?php

use App\Core\Config;

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
    <link type="text/css" rel="stylesheet" href="<?= assets_path('bootstrap/css/bootstrap.min.css'); ?>">
    <link type="text/css" rel="stylesheet" href="<?= assets_path('bootstrap/css/bootstrap-icons.css'); ?>">
    <link type="text/css" rel="stylesheet" href="<?= assets_path('css/styles.css'); ?>">
    <?php $this->content('header') ?>
    <meta name="author" content="<?= Config::get("author"); ?>">
</head>

<body style="background: #eee">

    <div class="container-fluid">
        <?php $this->content('content'); ?>
    </div>

    <script src="<?= assets_path('js/jquery-3.6.3.min.js'); ?>"></script>
    <script src="<?= assets_path('bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>

    <?php $this->content('script') ?>
</body>

</html>