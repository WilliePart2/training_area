<?php

namespace frontend\modules\common_actions;

use frontend\modules\common_actions\BaseAction;
use frontend\models\TrainingPlanManager;

class SetTrainingPlanAsComplete extends BaseAction
{
    public function run()
    {
        if (!\Yii::$app->getRequest()->getIsPost()) { return; }
        $data = json_decode(\Yii::$app->getRequest()->getRawBody(), true);
        $this->model = TrainingPlanManager::className();
        $this->scenario = TrainingPlanManager::MACROCICLE_MANIPULATION;
        $this->action = 'setTrainingPlanAsCompleted';
        $this->params = [
            \Yii::$app->user->identity->getId(),
            $data['TrainingPlanManager']['sessionId']
        ];
        return $this->preparedRun();
    }
}