<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $post app\models\Post */

$this->title = 'Редактировать пост';
?>

    <h2>Редактировать пост</h2>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($post, 'message')->textarea(['rows' => 6]) ?>

    <div class="form-group mt-2">
        <?= Html::submitButton('Сохранить изменения', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Отмена', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

<?php ActiveForm::end(); ?>