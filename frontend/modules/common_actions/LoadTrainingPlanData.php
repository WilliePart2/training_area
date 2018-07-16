<?php

namespace frontend\modules\common_actions;

use frontend\modules\common_actions\BaseAction;
use frontend\models\TrainingPlanManager;

class LoadTrainingPlanData extends BaseAction
{
    public function run($id)
    {
        $model = new TrainingPlanManager();
        $data = $model->getTrainingPlanInfo($id);
        if(!empty($data)) {
            return $this->generateResponse($data);
        }
        return $this->generateResponse();
    }
}
