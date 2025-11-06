<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $post app\models\Post */
/* @var $posts app\models\Post[] */

$this->title = 'StoryValut';
?>

<div class="row">
    <div class="col-md-7">
        <h2>Сообщения</h2>

        <?php foreach ($posts as $p): ?>
            <div class="card card-default mb-3">
                <div class="card-body">
                    <h5 class="card-title"><?= Html::encode($p->author) ?></h5>
                    <p><?= $p->message ?></p>
                    <p>
                        <small class="text-muted">
                            <?= $p->createdAtRelative ?> |
                            <?= $p->maskedIp ?> |
                            <?= $p->postsCountByIp ?> <?= \Yii::t('app', 'пост|поста|постов', $p->postsCountByIp) ?>
                        </small>
                    </p>
                    <p>
                        <?php if ($p->canEdit()): ?>
                            <?= Html::a('Редактировать', ['edit', 'token' => $p->edit_token], ['class' => 'btn btn-sm btn-warning']) ?>
                        <?php endif; ?>
                        <?php if ($p->canDelete()): ?>
                            <?= Html::a('Удалить', ['delete', 'token' => $p->delete_token], ['class' => 'btn btn-sm btn-danger']) ?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="col-md-5">
        <h2>Оставить сообщение</h2>

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($post, 'author')->textInput(['maxlength' => true]) ?>
        <?= $form->field($post, 'email')->input('email') ?>
        <?= $form->field($post, 'message')->textarea(['rows' => 6]) ?>
        <?= $form->field($post, 'captcha')->widget(Captcha::class, [
            'captchaAction' => 'post/captcha',
            'imageOptions' => ['class' => 'captcha-image'],
            'options' => ['class' => 'form-control', 'placeholder' => 'Введите текст с картинки']
        ]) ?>

        <div class="form-group mt-2">
            <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>