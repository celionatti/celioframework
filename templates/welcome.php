<?php


use App\Core\Config;
use App\Core\Forms\BootstrapForm;
use App\Core\Support\Helpers\TimeFormat;
use App\Core\Support\Helpers\StringFormat;


$this->setTitle(Config::get('title') . ' | Home Page');


?>


<?php $this->start('content') ?>
<h2>Welcome Page</h2>

<br>

<?= BootstrapForm::openForm("", "POST", 'multipart/form-data') ?>
<h3>Form Reg</h3>
<div class="row">
    <?= BootstrapForm::inputField("Surname", 'surname', '', ['class' => 'form-control'], ['class' => 'form-floating my-2 col'], $errors) ?>
    <?= BootstrapForm::inputField("Name", 'name', '', ['class' => 'form-control'], ['class' => 'form-floating my-2 col'], $errors) ?>
</div>
<?= BootstrapForm::submitButton("Register", "btn-dark w-100") ?>
<?= BootstrapForm::closeForm() ?>
<?php $this->end(); ?>