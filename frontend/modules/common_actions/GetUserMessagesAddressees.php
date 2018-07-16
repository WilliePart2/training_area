<?php

namespace frontend\modules\common_actions;

use frontend\models\UserCorrespondenceManager;
use frontend\modules\common_actions\BaseAction;

class GetUserMessagesAddressees extends BaseAction
{
    public function run($uid)
    {
        if (!\Yii::$app->getRequest()->getIsGet()) { return; }
        $model = new UserCorrespondenceManager();
        $result = $model->getAddressees($uid);
        if ($result) {
            return $this->generateResponse($result);
        }
        return $this->generateResponse();
    }
}