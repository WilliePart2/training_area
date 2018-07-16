<?php

namespace frontend\models;

use yii\db\ActiveRecord;
use frontend\models\Exersises;

class GroupsExersises extends ActiveRecord
{
    public function getExercises()
    {
        return $this->hasOne(Exersises::className(), ['group_id' => 'id']);
    }
    public static function getActiveGroups($rangeIds)
    {
        return self::find()->where(['in', 'id', $rangeIds])->all();
    }
}