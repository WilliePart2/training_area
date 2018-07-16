<?php

namespace frontend\modules\common_actions;

use frontend\models\TrainingPlanManager;
use frontend\modules\common_actions\BaseAction;

class GetCompletedTrainingPlans extends BaseAction
{
    public function run()
    {
        if (\Yii::$app->getRequest()->getIsGet()) {
            $model = new TrainingPlanManager();
            $model->scenario = TrainingPlanManager::MACROCICLE_MANIPULATION;
            $result = $model->getCompletedTrainingPlans(\Yii::$app->user->identity->getId());
            if (!empty($result)) {
                return $this->generateResponse($result);
            }
        }
        return $this->generateResponse();
    }
}