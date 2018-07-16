<?php

namespace frontend\modules\common_actions;

use frontend\helper_models\MessageFullModel;
use frontend\models\UserCorrespondenceManager;
use frontend\modules\common_actions\BaseAction;

class SetMessagesAsConsidered extends BaseAction
{
    public function run()
    {
        if (!\Yii::$app->getRequest()->getIsPost()) { return; }
        $data = json_decode(\Yii::$app->getRequest()->getRawBody(), true);
        $messages = array_map(function($row) {
            $data = $row;
            $data['roomReceiverId'] = null;
            return new MessageFullModel($data);
        }, $data['messages']);

        $this->model = UserCorrespondenceManager::className();
        $this->withoutValidate = true;
        $this->action = 'setMessagesAsConsidered';
        $this->params = [
            $messages,
            \Yii::$app->user->identity->getId()
        ];

        return parent::preparedRun();
    }
}