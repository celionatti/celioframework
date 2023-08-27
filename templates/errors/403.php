<?php

use App\Core\Config;

?>


<?php $this->start('content') ?>
<div class="container mx-auto">
    <a href="<?= Config::get("domain") ?>" class="bi bi-arrow-left-circle px-2 btn btn-sm btn-outline-primary my-1"> Back</a>

    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 420 230">
        <style>
            .error-code {
                font-size: 80px;
                fill: #FF0000;
            }

            .error-text {
                font-size: 24px;
                fill: #000000;
            }
        </style>
        <rect width="400" height="400" fill="#FFFFFF" />
        <text x="50%" y="50%" text-anchor="middle" class="error-code">403</text>
        <text x="50%" y="60%" text-anchor="middle" class="error-text">Forbidden</text>
    </svg>

</div>
<?php $this->end(); ?>