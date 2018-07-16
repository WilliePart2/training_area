<?php


namespace frontend\modules\common_actions;

use Codeception\Step\Comment;
use frontend\modules\common_actions\BaseAction;
use frontend\models\CommentManager;

class AddModifyTextToComment extends BaseAction
{
    public function run()
    {
        if(\Yii::$app->getRequest()->getIsPost()){
            $model = new CommentManager();
            $model->scenario = CommentManager::MODIFY_COMMENTS;
            if($model->load(json_decode(\Yii::$app->getRequest()->getRawBody(), true)) && $model->validate()){
                $result = $model->modifyComment();
                if(!empty($result)){
                    return $this->generateResponse($result);
                }
            }
        }
        return $this->generateResponse();
    }
}