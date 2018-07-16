<?php

namespace frontend\modules\common_actions;

use frontend\models\PostManager;
use frontend\modules\common_actions\BaseAction;

class ManageUserPostLikeDislike extends BaseAction
{
    public function run()
    {
        if (!\Yii::$app->getRequest()->getIsPost()) { return; }

        $data = \json_decode(\Yii::$app->getRequest()->getRawBody(), true);
        $this->model = PostManager::className();
        $this->withoutValidate = true;
        switch ($data['flag']) {
            case 'LIKE': $this->action = 'setLike'; break;
            case 'DISLIKE': $this->action = 'setDislike'; break;
        }
        $this->params = [
            $data['postId'],
            $data['userId']
        ];
        return parent::preparedRun();
    }
}