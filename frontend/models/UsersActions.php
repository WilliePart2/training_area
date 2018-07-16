<?php

namespace frontend\models;

use yii\db\ActiveRecord;

class UsersActions extends ActiveRecord
{
    const SUBSCRIBE_ON_USER = 'SUBSCRIBE_ON_USER';
    public function createUserAction($actionType, $initUserId, $tarUserId)
    {
        switch ($actionType) {
            case self::SUBSCRIBE_ON_USER: $this->createSubscribeAction($initUserId, $tarUserId); break;
        }
    }
    public function createSubscribeAction($initUserId, $tarUserId)
    {

    }
}