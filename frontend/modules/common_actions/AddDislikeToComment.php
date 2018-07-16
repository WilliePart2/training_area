<?php

namespace frontend\modules\common_actions;

use frontend\modules\common_actions\BaseAction;
use frontend\models\CommentManager;

class AddDislikeToComment extends BaseAction
{
    public function run()
    {
        $this->model = CommentManager::className();
        $this->action = 'addDislike';
        $this->scenario = CommentManager::MANAGE_COMMENTS;
        return parent::preparedRun();
    }
}