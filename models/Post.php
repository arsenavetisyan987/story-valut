<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveRecord;

class Post extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%post}}';
    }

    public function rules()
    {
        return [
            [['author', 'email', 'message'], 'required'],
            ['author', 'string', 'min' => 2, 'max' => 15],
            ['message', 'string', 'min' => 5, 'max' => 1000],
            ['email', 'email'],
            ['message', 'trim'],
            ['message', 'match', 'pattern' => '/\S+/', 'message' => 'Message cannot be only spaces.'],
        ];
    }

    /**
     * Очистка HTML, разрешая только <b>, <i>, <s>
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        $this->message = strip_tags($this->message, '<b><i><s>');

        return true;
    }

    /**
     * Проверка частоты публикации по IP
     * Возвращает true если можно отправлять, false — если нет
     */
    public static function canPost($ip)
    {
        $lastPost = self::find()->where(['ip' => $ip])->orderBy(['created_at' => SORT_DESC])->one();
        if (!$lastPost) {
            return true;
        }
        return (time() - $lastPost->created_at) >= 180; // 180 секунд = 3 минуты
    }
}
