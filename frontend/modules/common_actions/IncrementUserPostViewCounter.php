<?php

namespace frontend\modules\common_actions;

use frontend\models\PostManager;
use frontend\modules\common_actions\BaseAction;

class IncrementUserPostViewCounter extends BaseAction
{
    public function run()
    {
        if (!\Yii::$app->getRequest()->getIsPost()) { return; }
        $data = \json_decode(\Yii::$app->getRequest()->getRawBody(), true);

        $this->model = PostManager::className();
        $this->withoutValidate = true;
        $this->action = 'incrementViewCounter';
        $this->params = [
            $data['postId'],
            \Yii::$app->user->identity->getId()
        ];
        return parent::preparedRun();
    }
}