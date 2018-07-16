<?php

namespace frontend\modules\common_actions;

use frontend\modules\common_actions\BaseAction;
use frontend\models\UserInfoManager;

class SetUserInfoFieldValue extends BaseAction
{
    public function run()
    {
        if (!\Yii::$app->getRequest()->getIsPost()) { return; }
        $data = \json_decode(\Yii::$app->getRequest()->getRawBody(), true);
        $this->model = UserInfoManager::className();
        $this->action = 'setFieldValue';
        $this->params = [
            $data['UserInfoManager']['fieldId'],
            $data['UserInfoManager']['value'],
            \Yii::$app->user->identity->getId(),
            $data['UserInfoManager']['isFirstInsert'],
            $data['UserInfoManager']['recordId']
        ];
        return parent::preparedRun();
    }
}