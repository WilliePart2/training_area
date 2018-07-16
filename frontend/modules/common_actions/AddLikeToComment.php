<?php

namespace frontend\modules\common_actions;

use frontend\modules\common_actions\BaseAction;
use frontend\models\CommentManager;

class AddLikeToComment extends BaseAction
{
    public function run()
    {
        $this->model = CommentManager::className();
        $this->action = 'addLike';
        $this->scenario = CommentManager::MANAGE_COMMENTS;
        return parent::preparedRun();
    }
}