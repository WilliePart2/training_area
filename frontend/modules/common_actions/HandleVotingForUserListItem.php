<?php

namespace frontend\modules\common_actions;

use frontend\models\PostManager;
use frontend\modules\common_actions\BaseAction;
use frontend\helper_models\ListModel;

class HandleVotingForUserListItem extends BaseAction
{
    public function run()
    {
        if (!\Yii::$app->getRequest()->getIsPost()) { return; }
        $data = \json_decode(\Yii::$app->getRequest()->getRawBody(), true);
        $this->model = PostManager::className();
        $this->withoutValidate = true;
        switch ($data['flag']) {
            case 'set': $this->action = 'setVoteForListItem'; break;
            case 'remove': $this->action = 'dropVoteFromListItem'; break;
        }
        $this->params = [
            new ListModel($data['post']),
            \Yii::$app->user->identity->getId()
        ];
        return parent::preparedRun();
    }
}