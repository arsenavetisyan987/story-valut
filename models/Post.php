<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveRecord;

class Post extends ActiveRecord
{
    // Виртуальное свойство для captcha (если ещё не добавлено)
    public $captcha;

    /**
     * Возвращает IP с маской
     */
    public function getMaskedIp()
    {
        if (filter_var($this->ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $parts = explode('.', $this->ip);
            $parts[2] = '**';
            $parts[3] = '**';
            return implode('.', $parts);
        } elseif (filter_var($this->ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $parts = explode(':', $this->ip);
            for ($i = count($parts) - 4; $i < count($parts); $i++) {
                $parts[$i] = '****';
            }
            return implode(':', $parts);
        }
        return $this->ip;
    }

    /**
     * Возвращает relative time
     */
    public function getCreatedAtRelative()
    {
        $diff = time() - $this->created_at;

        if ($diff < 60) return "$diff секунд назад";
        if ($diff < 3600) return floor($diff / 60) . " минут назад";
        if ($diff < 86400) return floor($diff / 3600) . " часов назад";
        return floor($diff / 86400) . " дней назад";
    }

    /**
     * Подсчёт количества постов автора по IP
     */
    public function getPostsCountByIp()
    {
        return self::find()->where(['ip' => $this->ip])->count();
    }


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
            ['captcha', 'captcha'],
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
