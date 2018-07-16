<?php

namespace frontend\models;

use yii\db\ActiveRecord;
use frontend\models\Users;
use frontend\models\MacrocicleCommentsAddingText;

class MacrocicleComments extends  ActiveRecord
{
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'users_id']);
    }
    public function getAddingText()
    {
        return $this->hasMany(MacrocicleCommentsAddingText::className(), ['comment_id' => 'id']);
    }
}