<?php

namespace frontend\models;

use yii\db\ActiveRecord;
use frontend\models\UserTrainingPlan;

class BaseTrainingPlan extends ActiveRecord
{
    public static function checkBasePlan($trainingExerciseId, $microcicleId)
    {
        return self::find()->where(['microcicle_id' => $microcicleId, 'training_exersise_id' => $trainingExerciseId])->exists();
    }
    public static function getMaxOrder($trainingExerciseId, $microcicleId)
    {
        return self::find()->where(['microcicle_id' => $microcicleId, 'training_exersise_id' => $trainingExerciseId])->max('[[order]]');
    }

    /** Методы связи с таблицами */
    public function getUserTrainingPlans()
    {
        return $this->hasMany(UserTrainingPlan::className(), ['base_training_plan_id' => 'id']);
    }
}