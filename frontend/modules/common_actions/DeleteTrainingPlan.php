<?php

namespace frontend\modules\common_actions;

use frontend\modules\common_actions\BaseAction;
use frontend\models\TrainingPlanManager;

class DeleteTrainingPlan extends BaseAction
{
    public function run()
    {
        if(\Yii::$app->getRequest()->getIsPost()){
            $model = new TrainingPlanManager();
            $model->scenario = TrainingPlanManager::DELETE_MACROCICLE;
            if($model->load(json_decode(\Yii::$app->getRequest()->getRawBody(), true)) && $model->validate()){
                $result = $model->deleteTrainingPlan();
                if(!empty($result)) {
                    return $this->generateResponse($result);
                }
            }
            return $this->generateResponse();
        }
    }
}