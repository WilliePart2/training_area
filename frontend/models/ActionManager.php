<?php

namespace frontend\models;

use  yii\base\Model;
use frontend\models\UsersActions;
use frontend\models\PostActions;
use frontend\models\MacrocicleActions;

class ActionManager extends Model
{

    const CREATE_POST = 'CREATE_POST';
    const ALTER_POST = 'ALTER_POST';
    /*const DROP_POST = 'DROP_POST';*/

    const SUBSCRIBE_ON_USER = 'SUBSCRIBE_ON_USER';

    public function getUserNews($userId)
    {

    }
}