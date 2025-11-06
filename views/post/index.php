<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $posts app\models\Post[] */

$this->title = 'Сообщения';
?>

<h1><?= Html::encode($this->title) ?></h1>

<?php foreach ($posts as $post): ?>
    <div class="card card-default mb-3">
        <div class="card-body">
            <h5 class="card-title"><?= Html::encode($post->author) ?></h5>
            <p><?= $post->message ?></p>
            <p>
                <small class="text-muted">
                    <?= $post->createdAtRelative ?> |
                    <?= $post->maskedIp ?> |
                    <?= $post->postsCountByIp ?> <?= \Yii::t('app', 'пост|поста|постов', $post->postsCountByIp) ?>
                </small>
            </p>
        </div>
    </div>
<?php endforeach; ?>