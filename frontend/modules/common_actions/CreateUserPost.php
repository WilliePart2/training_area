<?php

namespace frontend\modules\common_actions;

use frontend\models\PostManager;
use frontend\modules\common_actions\BaseAction;

class CreateUserPost extends BaseAction
{
    public function run()
    {
        if (!\Yii::$app->getRequest()->getIsPost()) { return; }
        $this->model = PostManager::className();
        $this->scenario = PostManager::CREATE_POST;
        $this->action = 'createPost';
        $this->params = \Yii::$app->user->identity->getId();
        return parent::preparedRun();
    }
}