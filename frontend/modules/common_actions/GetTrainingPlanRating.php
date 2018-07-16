<?php

namespace frontend\modules\common_actions;

use frontend\modules\common_actions\BaseAction;
use frontend\models\TrainingPlanManager;

class GetTrainingPlanRating extends BaseAction
{
    public function run($planId)
    {
        if (\Yii::$app->getRequest()->getIsGet()) {
            $model = new TrainingPlanManager();
            $result = $model->getTrainingPlanRating($planId);
            if ($result !== false) {
                return $this->generateResponse($result);
            }
        }
        return $this->generateResponse();
    }
}