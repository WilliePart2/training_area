<?php

namespace frontend\modules\common_actions;

use frontend\helper_models\MessageBaseModel;
use frontend\models\UserCorrespondenceManager;
use frontend\modules\common_actions\BaseAction;

class SendMessageToUser extends BaseAction
{
    public function run()
    {
        if (!\Yii::$app->getRequest()->getIsPost()) { return; }
        $this->model = UserCorrespondenceManager::className();
        $this->withoutValidate = true;
        $this->action = 'sendMessage';
        $this->params = new MessageBaseModel(\json_decode(\Yii::$app->getRequest()->getRawBody(), true));
        return parent::preparedRun();
    }
}