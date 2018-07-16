<?php

namespace frontend\modules\common_actions;

use frontend\modules\common_actions\BaseAction;
use frontend\models\TrainingPlanManager;

class LoadCurrentTrainingPlan extends BaseAction
{
    public function run($pId = null)
    {
        if(\Yii::$app->getRequest()->getIsGet()){
            $model = new TrainingPlanManager();
            $result = $model->getFullDataAboutTrainingPlan(
                empty($pId) ? \Yii::$app->user->identity->getId() : $pId
            );
            if($result !== false){
                return $this->generateResponse($result);
            }
        }
        return $this->generateResponse();
    }
}