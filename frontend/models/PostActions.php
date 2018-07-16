<?php

namespace frontend\models;

use yii\db\ActiveRecord;

class PostActions extends ActiveRecord
{
    const CREATE_POST = 'CREATE_POST';
    const ALTER_POST = 'ALTER_POST';
}
