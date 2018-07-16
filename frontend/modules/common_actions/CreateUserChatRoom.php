<?php

namespace frontend\modules\common_actions;

use frontend\helper_models\UserBaseModel;
use frontend\models\UserCorrespondenceManager;
use frontend\modules\common_actions\BaseAction;

class CreateUserChatRoom extends BaseAction
{
    public function run()
    {
        if (!\Yii::$app->getRequest()->getIsPost()) { return; }
        $data = \json_decode(\Yii::$app->getRequest()->getRawBody(), true);
        $this->model = UserCorrespondenceManager::className();
        $this->withoutValidate = true;
        $this->action = 'createChatRoom';
        $this->params = [
            \Yii::$app->user->identity->getId(),
            array_map(function($recipient) {
                return new UserBaseModel(['id' => $recipient]);
            }, $data['recipients']),
            $data['message'],
            $data['topic']
        ];
        return parent::preparedRun();
    }
}