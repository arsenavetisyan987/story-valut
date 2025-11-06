<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use yii\captcha\CaptchaAction;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
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


    public function actions()
    {
        return [
            'captcha' => [
                'class' => CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }


    public function actionIndex()
    {
        $post = new Post();

        if ($post->load(Yii::$app->request->post())) {

            $ip = Yii::$app->request->userIP;
            if (!Post::canPost($ip)) {
                $lastPost = Post::find()->where(['ip' => $ip])->orderBy(['created_at' => SORT_DESC])->one();
                $nextTime = $lastPost->created_at + 180;
                $wait = $nextTime - time();
                Yii::$app->session->setFlash('error', "Подождите $wait секунд до следующей публикации.");
            } else {
                $post->ip = $ip;
                $post->created_at = time();

                if ($post->save()) {
                    Yii::$app->session->setFlash('success', 'Пост успешно опубликован!');
                    return $this->refresh();
                }
            }
        }

        $posts = Post::find()->where(['deleted_at' => null])->orderBy(['created_at' => SORT_DESC])->all();

        return $this->render('index', [
            'post' => $post,
            'posts' => $posts,
        ]);
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
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionEdit($token)
    {
        $post = Post::find()->where(['edit_token' => $token, 'deleted_at' => null])->one();
        if (!$post || !$post->canEdit()) {
            throw new NotFoundHttpException('Пост не найден или время редактирования истекло.');
        }

        if ($post->load(Yii::$app->request->post()) && $post->validate()) {
            $post->save();
            Yii::$app->session->setFlash('success', 'Пост обновлён!');
            return $this->redirect(['index']);
        }

        return $this->render('edit', ['post' => $post]);
    }

    public function actionDelete($token)
    {
        $post = Post::find()->where(['delete_token' => $token, 'deleted_at' => null])->one();
        if (!$post || !$post->canDelete()) {
            throw new NotFoundHttpException('Пост не найден или время удаления истекло.');
        }

        if (Yii::$app->request->post('confirm')) {
            $post->deleted_at = time(); // soft-delete
            $post->save();
            Yii::$app->session->setFlash('success', 'Пост удалён!');
            return $this->redirect(['index']);
        }

        return $this->render('delete', ['post' => $post]);
    }

}