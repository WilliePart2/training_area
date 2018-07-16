<?php

namespace frontend\modules\common_actions;

use frontend\modules\common_actions\BaseAction;
use frontend\models\UserCorrespondenceManager;

class GetUsersCorrespondences extends BaseAction
{
    public function run()
    {
        if (!\Yii::$app->getRequest()->getIsPost()) { return; }

        $data = \json_decode(\Yii::$app->getRequest()->getRawBody(), true);
        $this->model = UserCorrespondenceManager::className();
        $this->withoutValidate = true;
        $this->action = 'getCorrespondences';
        $this->params = $data['requestOwnerId'];
        return $this->preparedRun();
    }
}