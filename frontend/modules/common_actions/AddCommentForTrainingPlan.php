<?php

namespace frontend\modules\common_actions;

use frontend\modules\common_actions\BaseAction;
use frontend\models\CommentManager;

class AddCommentForTrainingPlan extends BaseAction
{
    public function run()
    {
        if(\Yii::$app->getRequest()->getIsPost()){
            $model = new CommentManager();
            $model->scenario = CommentManager::CREATE_COMMENTS;
            if($model->load(json_decode(\Yii::$app->getRequest()->getRawBody(), true)) && $model->validate()){
                $result = $model->createComment();
                if(!empty($result)){
                    return $this->generateResponse($result);
                }
            }
        }
        return $this->generateResponse();
    }
}