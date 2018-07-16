<?php

namespace backend\models;

use yii\db\ActiveRecord;
use backend\models\Exersises;

class GroupsExersises extends ActiveRecord
{
    public function getExercises()
    {
        return $this->hasMany(Exersises::className(), ['group_id' => 'id']);
    }
    public function getAllGroups()
    {
        return self::find()->all();
    }
    public function getCountGroups()
    {
        return self::find()->count();
    }
    public function deleteGroup($groupId)
    {
        $group = self::findOne($groupId);

        $result = false;
        if($group) {
            $result = $group->delete();
        }

        return $result ? true : false;
    }
}