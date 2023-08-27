<?php


use App\Core\Config;

$this->setTitle(Config::get('title') . ' | Admin | Dashboard');


?>

<?php $this->start('content') ?>
<h2>Admin Dashboard</h2>
<?php $this->end() ?>