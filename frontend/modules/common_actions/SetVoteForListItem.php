<?php

namespace frontend\modules\common_actions;

use frontend\models\PostManager;
use frontend\modules\common_actions\BaseAction;

class SetVoteForListItem extends BaseAction
{
    public function run()
    {
        if (!\Yii::$app->getRequest()->getIsPost()) { return; }
        $this->model = PostManager::className();
        $this->withoutValidate = true;
        $this->action = '';
    }
}