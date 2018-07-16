<?php

namespace frontend\modules\common_actions;

use frontend\modules\common_actions\BaseAction;
use frontend\models\ExerciseManager;

class AddExerciseToLayout extends BaseAction
{
    public function run()
    {
        if(\Yii::$app->getRequest()->getIsPost()){
            $model = new ExerciseManager();
            $model->scenario = ExerciseManager::MANAGE_LAYOUT;
            $data = \json_decode(\Yii::$app->getRequest()->getRawBody(), true);
            if($model->load($data) && $model->validate()) {
                $result = $model->manageLayoutForTrainingPlan();
                if(!empty($result)){
                    return $this->generateResponse($result);
                }
            }
        }
        return $this->generateResponse();
    }
}