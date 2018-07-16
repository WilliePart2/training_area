<?php

namespace frontend\modules\common_actions;

use frontend\modules\common_actions\BaseAction;
use frontend\models\AvatarManager;

class SetAvatarAsCurrent extends BaseAction
{
    public function run()
    {
        if (!\Yii::$app->getRequest()->getIsPost()) { return; }
        $this->model = AvatarManager::className();
        $this->action = 'setAvatarToUser';
        $this->scenario = AvatarManager::SET_AVATAR;
        $this->params = \Yii::$app->user->identity->getId();
        return parent::preparedRun();
    }
}