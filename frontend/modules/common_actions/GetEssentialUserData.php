<?php

namespace frontend\modules\common_actions;

use frontend\models\UsersManager;
use frontend\modules\common_actions\BaseAction;

class GetEssentialUserData extends BaseAction
{
    public function run($uid)
    {
        if (!\Yii::$app->getRequest()->getIsGet()) { return; }
        $model = new UsersManager();
        $result = $model->getEssentialUserData($uid, \Yii::$app->user->identity->getId());
        if (!empty($result)) {
            return $this->generateResponse($result);
        }
        return $this->generateResponse();
    }
}