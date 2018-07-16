<?php

namespace frontend\modules\common_actions;

use frontend\modules\common_actions\BaseAction;
use frontend\models\UsersManager;

class GetUsersList extends BaseAction
{
    public function run($offset, $limit)
    {
        if (!\Yii::$app->getRequest()->getIsGet()) { return; }
        $model = new UsersManager();
        $result = $model->getAllUsers($offset, $limit);
        return $this->generateResponse($result);
    }
}