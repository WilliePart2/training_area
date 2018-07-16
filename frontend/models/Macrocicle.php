<?php

namespace frontend\models;

use yii\db\ActiveRecord;
use frontend\models\Users;
use frontend\models\MacrocicleUsers;
use frontend\models\TrainingExersise;
use frontend\models\MacrocicleTrainingExersise;
use frontend\models\MacrocicleRating;

class Macrocicle extends ActiveRecord
{
    public static function findAllMacrocicleByMentorId($mentorId, $offset, $limit)
    {
        return self::find()->where(['mentor_id' => $mentorId, 'valid' => 1])->limit($limit)->offset($offset)->all();
    }
    public static function findAllMacrocicleByByUserId($id)
    {
        return self::find()->joinWith('trainingUsers')->where(['trainingUsers.user_id' => $id])->all();
    }
    public static function findOneMacrocicleById($planId)
    {
        return self::find()->with('trainingExercises')->where(['id' => $planId])->one();
    }
    public function getTrainingUsers()
    {
        return $this->hasMany(Users::className(), ['id' => 'users_id'])->viaTable('macrocicle_users', ['macrocicle_id' => 'id']);
    }
    public static function getLatestInsert($mentorId)
    {
        $counter = intval(self::find()->where(['mentor_id' => $mentorId])->max('[[counter]]'));
        return self::find()->where(['counter' => $counter, 'mentor_id' => $mentorId])->one();
    }
    public static function getCount($mentorId)
    {
        return self::find()->where(['mentor_id' => $mentorId, 'visible' => 1])->count();
    }

    /** Методы связи с таблицей */
    /** Мето связываеться с тренировочным шаблоном */
    public function getTrainingExercises()
    {
        return $this->hasMany(TrainingExersise::className(), ['id'=> 'training_exersise_id']) // указываем как training_exersise связан в промежуточной таблице
            ->viaTable('macrocicle_training_exersise', ['macrocicle_id' => 'id']); // указываем как связана текущая таблица с промежуточной
            // промежуточную таблицу нужно указывать строкой иначе будет ошибка
    }
    public function getRating() {
        return $this->hasMany(MacrocicleRating::className(), ['macrocicle_id' => 'id']);
    }

    /**
     * methods with make some computations
     */
    public static function computateRating($macrocicle)
    {
        return (function ($macrocicle) {
            $countItems = 0;
            $totalRating = array_reduce($macrocicle->rating, function($store, $item) use (&$countItems) {
                $countItems += 1;
                $store += $item->rating;
                return $store;
            }, 0);
            return $totalRating ? $totalRating / $countItems : 0;
        })($macrocicle);
    }
    public static function getMacrocicleInfo(\frontend\models\Macrocicle $macrocicle)
    {
        return [
            'id' => $macrocicle->id,
            'name' => $macrocicle->name,
            'readme' => $macrocicle->readme,
            'category' => $macrocicle->category,
            'rating' => self::computateRating($macrocicle),
        ];
    }
}