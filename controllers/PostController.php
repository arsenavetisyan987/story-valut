<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use yii\captcha\CaptchaAction;
use yii\web\Controller;
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
                $post->created_at = date('Y-m-d H:i:s');

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
            $model->created_at = date('Y-m-d H:i:s');

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

        if (!$post) {
            Yii::$app->session->setFlash('error', 'Пост не найден. Возможно, ссылка неправильная.');
            return $this->redirect(['index']);
        }

        if (!$post->canEdit()) {
            Yii::$app->session->setFlash('error', 'Время редактирования поста истекло. 
                Редактирование возможно только в течение 12 часов после публикации.');
            return $this->redirect(['index']);
        }

        if (Yii::$app->request->isPost) {
            if ($post->load(Yii::$app->request->post())) {

                if ($post->validate(['message'])) {
                    if ($post->save(false)) {
                        Yii::$app->session->setFlash('success', 'Пост успешно обновлён!');
                    } else {
                        Yii::$app->session->setFlash('error', 'Не удалось сохранить изменения.');
                    }
                } else {
                    Yii::$app->session->setFlash('error', 'Ошибка: сообщение не прошло проверку. 
                    Длина текста должна быть 5-1000 символов и не может состоять только из пробелов.');
                }

                return $this->redirect(['index']);
            }
        }

        return $this->render('edit', ['post' => $post]);
    }


    public function actionDelete($token)
    {
        $post = Post::find()->where(['delete_token' => $token, 'deleted_at' => null])->one();

        if (!$post) {
            Yii::$app->session->setFlash('error', 'Пост не найден. Возможно, ссылка неправильная.');
            return $this->redirect(['index']);
        }

        if (!$post->canDelete()) {
            Yii::$app->session->setFlash('error', 'Время удаления поста истекло. Удаление возможно только в течение 14 дней после публикации.');
            return $this->redirect(['index']);
        }

        if (Yii::$app->request->isPost) {
            if (Yii::$app->request->post('confirm')) {
                $post->deleted_at = date('Y-m-d H:i:s');
                if ($post->save(false)) {
                    Yii::$app->session->setFlash('success', 'Пост успешно удалён!');
                } else {
                    Yii::$app->session->setFlash('error', 'Не удалось удалить пост.');
                }
            } else {
                Yii::$app->session->setFlash('error', 'Действие не подтверждено.');
            }
            return $this->redirect(['index']);
        }

        return $this->render('delete', ['post' => $post]);
    }
}