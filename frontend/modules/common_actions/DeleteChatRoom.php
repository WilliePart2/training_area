<?php

namespace frontend\modules\common_actions;

use frontend\models\UserCorrespondenceManager;
use frontend\modules\common_actions\BaseAction;

class DeleteChatRoom extends BaseAction
{
    public function run()
    {
        if (!\Yii::$app->getRequest()->getIsPost()) {
            return;
        }
        $data = \json_decode(\Yii::$app->getRequest()->getRawBody(), true);
        $this->model = UserCorrespondenceManager::className();
        $this->withoutValidate = true;
        $this->action = 'removeChatRoom';
        $this->params = [
            $data['roomId'],
            \Yii::$app->user->identity->getId()
        ];
        return parent::preparedRun();
    }
}