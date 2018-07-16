<?php

namespace frontend\models;

use yii\db\ActiveRecord;
use frontend\models\TrainingExersise;
use frontend\models\BaseTrainingPlan;
use yii\debug\models\search\Base;

class TrainingExersiseTraining extends ActiveRecord
{
    public function getTrainingExercise()
    {
        return $this->hasOne(TrainingExersise::className(), ['id' => 'training_exersise_id']);
    }
    public function getRelatedPlans()
    {
        return $this->hasMany(BaseTrainingPlan::className(), ['exersise_unique_id' => 'unique_id']);
    }
}