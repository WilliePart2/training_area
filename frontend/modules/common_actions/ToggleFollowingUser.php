<?php

namespace frontend\modules\common_actions;

use frontend\models\UsersManager;
use frontend\modules\common_actions\BaseAction;

class ToggleFollowingUser extends BaseAction
{
    public function run()
    {
        if (!\Yii::$app->getRequest()->getIsPost()) { return; }
        $data = \json_decode(\Yii::$app->getRequest()->getRawBody(), true);
        $model = new UsersManager();
        $result = $model->followUser($data['followedId'], $data['followerId']);
        if (!empty($result)) {
            return $this->generateResponse($result);
        }
        return $this->generateResponse();
    }
}