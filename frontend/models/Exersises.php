<?php

namespace frontend\models;

use yii\db\ActiveRecord;
use frontend\models\GroupsExersises;

class Exersises extends ActiveRecord
{
    public static function getAllExercises()
    {
        return self::find()->with('groups')->all();
    }
    public function getGroups()
    {
        return $this->hasOne(GroupsExersises::className(), ['id' => 'group_id']);
    }
}