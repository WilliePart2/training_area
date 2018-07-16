<?php

namespace frontend\modules\common_actions;

use frontend\modules\common_actions\BaseAction;
use frontend\models\ExerciseManager;

class LoadBaseLayoutForTrainingPlan extends BaseAction
{
    public function run($id)
    {
        $model = new ExerciseManager();
        $result = $model->getTrainingPlanLayout($id);
        if($result !== false){
            return $this->generateResponse($result);
        }
        return $this->generateResponse();
    }
}
