<?php

namespace frontend\models;

use yii\db\ActiveRecord;
use frontend\models\Users;
use frontend\models\Mentors;

class UsersMentors extends ActiveRecord
{
    const PENDING_REQUEST_TO_USER = 2;
    const ACTIVE_USER = 1;
    const UNACTIVE_USER = 0;
    const MENTOR_INITIATOR = 1;
    const USER_INITIATOR = 2;

    /** Метод поиска запросов на связывание */
    public static function findActiveBindingRequest($userId, $mentorId, $initialithator)
    {
        return self::find()->where(['users_id' => $userId, 'mentor_id' => $mentorId, 'initialithator' => $initialithator, 'status' => 2])->all();
    }

    /** Метод получения всех пользователй связь с которыми ожидает подтверждения */
    public static function findAllRequestToRelationByMentor($mentorId, $offset, $limit)
    {
        return self::find()
            ->with(['users' => function (\yii\db\ActiveQuery $query) {
                $query->with([
                    'rating',
                    'mentor',
                    'padawans',
                    'userMacrocicles',
                    'trainingPlans'
                ]);
            }])
            ->where(['mentors_id' => $mentorId, 'status' => UsersMentors::PENDING_REQUEST_TO_USER])
            ->offset($offset)
            ->limit($limit)
            ->orderBy([
                'id' => SORT_DESC
            ])
            ->all();
    }
    /** Метод полуения количества завпросов от пользователей */
    public static function getCountRequestsFromUsers($mentorId)
    {
        return self::find()->where(['mentors_id' => $mentorId, 'status' => 2, 'initialithator' => 2])->distinct()->count();
    }
    /** Метод получения количества запросов от менторв */
    public static function getCountRequestsFromMentor($mentorId)
    {
        return self::find()->where(['mentors_id' => $mentorId, 'status' => 2, 'initialithator' => 1])->distinct()->count();
    }

    /** Метод возвращает текущих подопечных ментора */
    public static function findActiveUsers($mentorId, $offset, $limit)
    {
        $query = self::find()->where(['mentors_id' => $mentorId, 'status' => 1])
                            ->with('users')
                            ->offset($offset);
            if($limit) {
                $query->limit($limit);
            }
            $result = $query->all();
            return $result;
    }
    /** Метод возвращает количество текущих подопечных ментора */
    public static function getCountActiveUsers($mentorId)
    {
        return self::find()->where(['mentors_id' => $mentorId, 'status' => 1])->count();
    }

    /** Пользовательские запросы */
    /**
     * Метод возвращает ментора текущего пользователя
     * Пока что пользователь может иметь только одного ментора
     */
    public static function findActiveMentor($userId)
    {
        return self::find()->with(['mentors' => function ($query) {
            return $query->with([
                'rating',
                'mentor',
                'padawans',
                'userMacrocicles',
                'trainingPlans'
            ]);
        }])->where([
            'users_id' => $userId,
            'status' => UsersMentors::ACTIVE_USER
        ])->one();
    }
    /**
     * Метод возвращает ментора которому пользователь отправит запрос на связывание
     * Пользователь может иметь только одного ментора поэтому возвращаеться единственная запись
    */
    public static function findRequestToMentor($userId)
    {
        return self::find()->with(['mentors' => function ($query) {
            $query->with([
                'rating',
                'mentor',
                'padawans',
                'userMacrocicles',
                'trainingPlans'
            ]);
        }])->where([
            'users_id' => $userId,
            'status' => UsersMentors::PENDING_REQUEST_TO_USER,
            'initialithator' => UsersMentors::USER_INITIATOR
        ])->one();
    }

    /** Методы связи */
    public function getUsers()
    {
        return $this->hasOne(Users::className(), ['id' => 'users_id']);
    }

    public function getUsersMacrocicles()
    {
        return $this->hasMany(MacrocicleUsers::className(), ['users_id' => 'users_id']);
    }

    /** deprecated */
    public function getMentors() // Можно еще добавить получение всех менторов которые были у пользователя
    {
        return $this->hasOne(Users::className(), ['id' => 'mentors_id']);
    }
}