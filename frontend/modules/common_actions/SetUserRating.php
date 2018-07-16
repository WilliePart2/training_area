<?php

namespace frontend\modules\common_actions;

use frontend\models\UsersManager;
use frontend\modules\common_actions\BaseAction;

class SetUserRating extends BaseAction
{
    public function run()
    {
        if (!\Yii::$app->getRequest()->getIsPost()) { return; }
        $data = \json_decode(\Yii::$app->getRequest()->getRawBody(), true);
        $this->withoutValidate = true;
        $this->model = UsersManager::className();
        $this->action = 'setRating';
        $this->params = [
            \Yii::$app->user->identity->getId(),
            $data['evaluateUserId'],
            $data['ratingValue']
        ];
        return parent::preparedRun();
    }
}