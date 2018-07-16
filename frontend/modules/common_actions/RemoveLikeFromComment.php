<?php

namespace frontend\modules\common_actions;

use frontend\modules\common_actions\BaseAction;
use frontend\models\CommentManager;

class RemoveLikeFromComment extends BaseAction
{
    public function run()
    {
        $this->model = CommentManager::className();
        $this->action = 'deleteLike';
        $this->scenario = CommentManager::MANAGE_COMMENTS;
        return parent::preparedRun();
    }
}