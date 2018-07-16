<?php

namespace frontend\models;

use yii\db\ActiveRecord;

class MacrocicleRating extends ActiveRecord
{
    public static function getAverageRatingTrainingPlan($macrocicleId)
    {
        return self::find()->where(['macrocicle_id' => $macrocicleId])->average('[[rating]]');
    }
}