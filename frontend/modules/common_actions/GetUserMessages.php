<?php

namespace frontend\modules\common_actions;

use frontend\models\UserCorrespondenceManager;
use frontend\modules\common_actions\BaseAction;

class GetUserMessages extends BaseAction
{
    public function run($roomId, $check = null, $from = null)
    {
        if (!\Yii::$app->getRequest()->getIsGet()) { return; }
        $model = new UserCorrespondenceManager();
        $result = $model->getMessages($roomId, $from, $check);
        if ($result) {
            return $this->generateResponse($result);
        }
        return $this->generateResponse();
    }
}