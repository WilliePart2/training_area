<?php

namespace frontend\modules\common_actions;

use frontend\modules\common_actions\BaseAction;
use frontend\models\TrainingPlanManager;

class SetTrainingPlanAsCurrent extends BaseAction
{
    public function run()
    {
        if(!\Yii::$app->getRequest()->getIsPost()) { return; }
        $this->model = TrainingPlanManager::className();
        $this->action = 'setTrainingPlanAsCurrent';
        $this->scenario = TrainingPlanManager::MACROCICLE_MANIPULATION;
        $this->params = \Yii::$app->user->identity->getId();
        return $this->preparedRun();
    }
}