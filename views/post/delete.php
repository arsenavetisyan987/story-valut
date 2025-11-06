<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $post app\models\Post */

$this->title = 'Удалить пост';
?>

    <h2>Удалить пост</h2>

    <p>Вы уверены, что хотите удалить этот пост?</p>

    <div class="card card-default mb-3">
        <div class="card-body">
            <h5 class="card-title"><?= Html::encode($post->author) ?></h5>
            <p><?= Html::encode($post->message) ?></p>
        </div>
    </div>

<?php $form = ActiveForm::begin(); ?>

<?= Html::hiddenInput('confirm', 1) ?>
    <div class="form-group">
        <?= Html::submitButton('Подтвердить удаление', ['class' => 'btn btn-danger']) ?>
        <?= Html::a('Отмена', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

<?php ActiveForm::end(); ?>