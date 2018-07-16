<?php

namespace backend\models;

use yii\db\ActiveRecord;
use backend\models\GroupsExersises;

class Exersises extends ActiveRecord
{
    public function getAllExercises()
    {
        return self::find()->with('groupExercise')->all();
    }
    public function getSomeExercises($offset, $limit, $group = null)
    {
        if(!empty($group)){
            return self::find()->with('groupExercise')->where(['group_id' => $group])->offset($offset)->limit($limit)->all();
        }
        return self::find()->with('groupExercise')->offset($offset)->limit($limit)->all();
    }
    public function getCountExercises($group = null)
    {
        if(!empty($group)){
            return self::find()->where(['group_id' => $group])->count();
        }
        return self::find()->count();
    }
    public function getGroupExercise()
    {
        return $this->hasOne(GroupsExersises::className(), ['id' => 'group_id']);
    }
    public function getNotEmptyGroups()
    {
        return self::find()->select('group_id')->distinct()->asArray()->column();
    }
}