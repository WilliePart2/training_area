<?php

namespace frontend\modules\common_actions;

use frontend\models\PostManager;
use frontend\modules\common_actions\BaseAction;

class GetUserPosts extends BaseAction
{
    public function run($offset, $limit, $uid = null)
    {
        if (\Yii::$app->getRequest()->getIsGet()) {
            $userId = isset($uid) && !empty($uid) ? $uid : \Yii::$app->user->identity->getId();
            $model = new PostManager();
            $result = $model->getPosts($userId, $offset, $limit);
            if (!empty($result)) {
                return $this->generateResponse($result);
            }
            return $this->generateResponse();
        }
    }
}