<?php

namespace frontend\models;

use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use frontend\models\Macrocicle;

class Users extends ActiveRecord implements IdentityInterface
{
    public static function findIdentity($id)
    {
        return self::findOne($id);
    }
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $id = \Yii::$app->cache->get($token);
        return self::findIdentity($id);
    }
    public static function findIdentityByUsername($username)
    {
        return self::find()->where(['username' => $username])->one();
    }
    public function getId()
    {
        return $this->getPrimaryKey();
    }
    public function getAuthKey()
    {
        return $this->auth_key;
    }
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
    public static function generateAuthKey()
    {
        return \Yii::$app->security->generateRandomString();
    }
    public static function generatePassword($password)
    {
        return \Yii::$app->security->generatePasswordHash($password);
    }
    public static function validatePassword($password, $passwordHash)
    {
        return \Yii::$app->security->validatePassword($password, $passwordHash);
    }
    /**
     * Обновляем токен доступа
    */
    public static function reNewToken()
    {
        $oldToken = \Yii::$app->getRequest()->getHeaders()->get('Authorization');

        $newToken = \Yii::$app->getSecurity()->generateRandomString();
        if(\Yii::$app->cache->exists($oldToken)) {
            \Yii::$app->cache->delete($oldToken);
        }
        \Yii::$app->cache->set($newToken, ['id' => \Yii::$app->user->getId()]);
        return $newToken;
    }

    /** Методы работ с таблицей пользователей */
    /** Получение списка пользователей */
    public static function findUsersList($offset, $limit)
    {
        return self::find()->with('macrocicles')->offset($offset)->limit($limit)->all();
    }
    /** Получение списка пользователей по частичному совпадению имени */
    public static function findUsersByPartOfName($partUsername, $offset = null, $limit = null)
    {
        $query = self::find()->where(['like', 'username', "$partUsername"]);
        if ($offset) {
            $query->offset($offset);
        }
        if ($limit) {
            $query->limit($limit);
        }
        return $query->all();
    }
    /** Метод возвращает общее количество пользователей */
    public static function getCountUsers()
    {
        return self::find()->count(); // Главное что бы сервер не лег....
    }

    /** Возвращает аватар пользователя */
    public function getAvatar()
    {
        $avatar = $this->avatar;
        $server = 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] ;
        $path = \Yii::getAlias((\Yii::$app->params['pathToAvatars']));
        if(empty($avatar)) {
            $avatar = $server . $path . \Yii::$app->params['defaultUserAvatar'];
        } else {
            $avatar = $server . $path . basename($this->avatar);
        }
        return $avatar;
    }

    /** Методы связывания таблиц */
    /** Связь к тренировочным планам пользователей */
    public function getMacrocicles()
    {
        return $this->hasMany(Macrocicle::className(), ['id' => 'macrocicle_id'])
            ->viaTable('macrocicle_users', ['users_id' => 'id']);
    }

    /** Связь к тренировочным планам менторов */
    public function getTrainingPlans()
    {
        return $this->hasMany(Macrocicle::className(), ['mentor_id' => 'id']);
    }

    public function getUserMacrocicles()
    {
        return $this->hasMany(MacrocicleUsers::className(), ['users_id' => 'id']);
    }

    /** Связь от менторов к их ученикам */
    public function getSubPadawans()
    {
        return $this->hasMany(UsersMentors::className(), ['mentors_id' => 'id'])
            ->andWhere(['status' => UsersMentors::ACTIVE_USER]);
    }
    public function getPadawans()
    {
        return $this->hasMany(Users::className(), ['id' => /*'id'*/'users_id'])
            ->via('subPadawans');
    }
    public function getMentor()
    {
        return $this->hasOne(Users::className(), ['id' => 'mentors_id'])
            ->viaTable('users_mentors', ['users_id' => 'id']);
    }
    public function getRating()
    {
        return $this->hasMany(UserRating::className(), ['users_id' => 'id']);
//            ->average('[[value]]');
    }

    /**
     * methods which perform computations
     * @users -  User object
     */
    public static function computeUserRating(\frontend\models\Users $user)
    {
        $rating = isset($user->rating) ? $user->rating : false;
        if (!$rating) { return 0;}
        return (function($rating){
            $countItems = 0;
            $totalRating = array_reduce($rating, function($store, $item) use (&$countItems) {
                $countItems += 1;
                $store += $item->value;
                return $store;
            }, 0);
            return $countItems > 0 ? $totalRating / $countItems : 0;
        })($rating);
    }
}