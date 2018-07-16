<?php

namespace frontend\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;

class Mentors extends ActiveRecord implements IdentityInterface
{
    public static function findIdentity($id)
    {
        return self::findOne($id);
    }
    public static function findIdentityByUsername($username)
    {
        return self::find()->where(['username' => $username])->one();
    }
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $id = \Yii::$app->cache->get($token);
        if(!empty($id)) {
            $identity = self::findIdentity($id);
            return $identity;
        }
        return null;
    }
    public function getId()
    {
        return $this->id;
    }
    public function getAuthKey()
    {
        return $this->auth_key;
    }
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
    public static function generatePassword($password)
    {
        return \Yii::$app->security->generatePasswordHash($password);
    }
    public static function validatePassword($passwordFromUser, $passwordFromDb)
    {
        return \Yii::$app->security->validatePassword($passwordFromUser, $passwordFromDb);
    }

    /** Методы работы с таблицей */
    /** Получение списка менторов */
    public static function findMentorList($offset, $limit)
    {
        return self::find()->offset($offset)->limit($limit)->all();
    }
    /** Получение списка менторов по частичному совпадению имени */
    public static function findMentorsByPartOfName($partUsername ,$offset, $limit)
    {
        return self::find()->offset($offset)->limit($limit)->where(['line', 'username', "$partUsername%"])->all();
    }
    /** Метод обправки запроса ментору на связывание */
    public static function sendBindingRequestToUser($userId)
    {

    }
}