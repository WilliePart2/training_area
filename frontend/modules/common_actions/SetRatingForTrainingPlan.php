<?php

namespace frontend\modules\common_actions;

use frontend\modules\common_actions\BaseAction;
use frontend\models\TrainingPlanManager;

class SetRatingForTrainingPlan extends BaseAction
{
    public function run()
    {
        if (\Yii::$app->getRequest()->getIsPost()) {
            $model = new TrainingPlanManager();
            $model->scenario = TrainingPlanManager::MACROCICLE_MANIPULATION;
            $data = \json_decode(\Yii::$app->getRequest()->getRawBody(), true);
            if ($model->load($data) && $model->validate()){
                $result = $model->addRatingForTrainingPlan(
                    \Yii::$app->user->identity->getId(),
                    $data['TrainingPlanManager']['rating']
                );
                if ($result !== false) {
                    return $this->generateResponse($result);
                }
            }
        }
        return $this->generateResponse();
    }
}