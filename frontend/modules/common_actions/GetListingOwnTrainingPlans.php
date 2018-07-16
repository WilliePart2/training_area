<?php

namespace frontend\modules\common_actions;

use yii\base\Action;
use frontend\models\TrainingPlanManager;
use frontend\models\Users;

class GetListingOwnTrainingPlans extends Action
{
    public function run($offset, $limit)
    {
        if(\Yii::$app->getRequest()->getIsGet()){
            $model = new TrainingPlanManager();
            $mentorId = \Yii::$app->user->identity->getId();
            $trainingPlans = $model->getListingOwnTrainingPlans($mentorId, $offset, $limit);
            $countTrainingPlans = $model->getCountOwnTrainingPlans($mentorId);
            if(!empty($trainingPlans) && !empty($countTrainingPlans)) {
                return $this->generateResponse([
                    'totalCount' => $countTrainingPlans,
                    'trainingData' => $trainingPlans
                ]);
            }
            return $this->generateResponse();
        }
        return \Yii::$app->getResponse()->data = file_get_contents(\Yii::getAlias('@frontend/web/index.html'), 'rt');
    }

    private function generateResponse($data= null)
    {
        return [
            'result' => empty($data) ? false : true,
            'accessToken' => Users::reNewToken(),
            'totalCount' => empty($data) ? null : $data['totalCount'],
            'trainingData' => empty($data) ? null : $data['trainingData']
        ];
    }
}