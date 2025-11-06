<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $post app\models\Post */
$this->title = 'Редактировать пост';
?>

<h1><?= Html::encode($this->title) ?></h1>

<?php $form = ActiveForm::begin(); ?>
<?= $form->field($post, 'message')->textarea(['rows' => 6]) ?>
<?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
<?php ActiveForm::end(); ?>
