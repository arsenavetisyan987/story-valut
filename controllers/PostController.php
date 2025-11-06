<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\Post;
use yii\filters\VerbFilter;

class PostController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['POST', 'GET'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $posts = Post::find()
            ->where(['deleted_at' => null])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        return $this->render('index', ['posts' => $posts]);
    }

    /**
     * Создание нового поста
     */
    public function actionCreate()
    {
        $model = new Post();

        if ($model->load(Yii::$app->request->post())) {

            $model->ip = Yii::$app->request->userIP;
            $model->created_at = time();

            if (!Post::canPost($model->ip)) {
                $lastPost = Post::find()->where(['ip' => $model->ip])->orderBy(['created_at' => SORT_DESC])->one();
                $nextTime = $lastPost->created_at + 180;
                Yii::$app->session->setFlash('error', "Вы можете отправить следующий пост не ранее " . date('H:i:s', $nextTime));
                return $this->refresh();
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Пост успешно отправлен!');
                return $this->refresh();
            }
        }

        return $this->render('create', ['model' => $model]);
    }
}