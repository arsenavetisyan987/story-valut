<?php

use yii\helpers\Html;

$this->title = 'Удалить пост';
?>

    <h1><?= Html::encode($this->title) ?></h1>

    <p>Вы уверены, что хотите удалить этот пост?</p>

<?= Html::beginForm() ?>
<?= Html::submitButton('Да, удалить', ['name' => 'confirm', 'class' => 'btn btn-danger']) ?>
<?= Html::a('Отмена', ['index'], ['class' => 'btn btn-secondary']) ?>
<?= Html::endForm() ?>