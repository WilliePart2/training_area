<?php

namespace frontend\modules\common_actions;

use frontend\models\AvatarManager;
use frontend\modules\common_actions\BaseAction;

class GetAvailableAvatars extends BaseAction
{
    public function run()
    {
        if (!\Yii::$app->getRequest()->getIsGet()) { return; }
        $model = new AvatarManager();
        $list = $model->getAvatarList();
        if (!empty($list)) {
            return $this->generateResponse($list);
        }
        return $this->generateResponse();
    }
}