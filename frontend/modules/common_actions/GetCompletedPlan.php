<?php

namespace frontend\modules\common_actions;

use frontend\modules\common_actions\BaseAction;
use frontend\models\TrainingPlanManager;

class GetCompletedPlan extends BaseAction
{
    public function run($id, $tSession, $pId = null)
    {
        if(\Yii::$app->getRequest()->getIsGet()){
            $model = new TrainingPlanManager();
            $model->scenario = TrainingPlanManager::MACROCICLE_MANIPULATION;
            $result = $model->getFullDataAboutTrainingPlan(
                empty($pId) ? \Yii::$app->user->identity->getId() : $pId,
                $id,
                $tSession
            );
            if (!empty($result)) {
                return $this->generateResponse($result);
            }
        }
        return $this->generateResponse();
    }
}