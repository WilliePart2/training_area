<?php

namespace frontend\modules\common_actions;

use frontend\modules\common_actions\BaseAction;
use frontend\models\UserInfoManager;

class GetFieldList extends BaseAction
{
    public function run()
    {
        if (!\Yii::$app->getRequest()->getIsGet()) { return; }
        $model = new UserInfoManager();
        $result = $model->getFieldList(\Yii::$app->user->identity->type);
        if (!empty($result)) {
            return $this->generateResponse($result);
        }
        return $this->generateResponse();
    }
}