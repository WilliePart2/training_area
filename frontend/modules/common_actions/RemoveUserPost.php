<?php

namespace frontend\modules\common_actions;

use frontend\models\PostManager;
use frontend\modules\common_actions\BaseAction;
use frontend\helper_models\BasePostModel;

class RemoveUserPost extends BaseAction
{
    public function run()
    {
        if (!\Yii::$app->getRequest()->getIsPost()) { return; }
        $model = new PostManager();
        $data = new BasePostModel(
            \json_decode(\Yii::$app->getRequest()->getRawBody(), true)['post']
        );
        $result = $model->removePost($data);
        if (!empty($result)) {
            return $this->generateResponse($result);
        }
        return $this->generateResponse();
    }
}