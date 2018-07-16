<?php

namespace frontend\modules\common_actions;

use frontend\modules\common_actions\BaseAction;
use frontend\models\CommentManager;

class GetCommentsForTrainingPlan extends BaseAction
{
    public function run($id, $offset, $limit)
    {
        $model = new CommentManager();
        $model->scenario = CommentManager::GET_COMMENTS;
        if($model->load(['CommentManager' => ['planId' => $id]]) && $model->validate()){
            $result = $model->getComments($offset, $limit);
            if(!empty($result)){
                return $this->generateResponse($result);
            }
        }
        return $this->generateResponse();
    }
}