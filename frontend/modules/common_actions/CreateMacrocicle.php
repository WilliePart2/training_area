<?php

namespace frontend\modules\common_actions;

use yii\base\Action;
use frontend\models\TrainingPlanManager;
use frontend\models\Users;

/**
 * Метод создает тренировочный план
 * @name - имя тренировочного плана
 * @readme - описание тренировочного плана
 * @visible - видимость тренировочного плана
 * @trainingPlanData - список упранений в виде упражнение + повторный максисмум для шаблона тренировочного плана
*/
class CreateMacrocicle extends Action
{
    public function run()
    {
        if(\Yii::$app->getRequest()->getIsPost()){
            $model = new TrainingPlanManager();
            $model->scenario = TrainingPlanManager::CREATE_MACROCICLE;
            $data = json_decode(\Yii::$app->getRequest()->getRawBody(), true);
            if($model->load($data) && $model->validate()){
                $result = $model->createTrainingPlan(
                    \Yii::$app->user->identity->getId(),
                    $data['TrainingPlanManager']['trainingPlanData']
                );
                if(!empty($result)) {
                    return $this->generateResonse($result);
                }
            }
            return $this->generateResonse();
        }
        return \Yii::$app->getResponse()->data = file_get_contents(\Yii::getAlias('@frontend/web/index.html'));
    }
    private function generateResonse($data= null)
    {
        return [
            'result' => $data ? true : false,
            'data' => $data,
            'accessToken' => Users::reNewToken()
        ];
    }
}