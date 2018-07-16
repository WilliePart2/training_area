<?php

namespace frontend\modules\common_actions;

use frontend\modules\common_actions\BaseAction;
use frontend\models\TrainingPlanManager;

class UpdateTrainingPlan extends BaseAction
{
    public function run()
    {
        if(\Yii::$app->getRequest()->getIsPost()){
            $model = new TrainingPlanManager();
            $model->scenario = TrainingPlanManager::SAVE_MACROCICLE_EDITION;
            $data = json_decode(\Yii::$app->getRequest()->getRawBody(), true);
            if($model->load($data) && $model->validate()){
                $result = $model->saveTrainingPlanEdition(
                    $data['TrainingPlanManager']['dataForInsert'],
                    $data['TrainingPlanManager']['dataForDelete']
                );
                if(!empty($result)){
                    return $this->generateResponse($result);
                }
            }
        }
        return $this->generateResponse();
    }
}