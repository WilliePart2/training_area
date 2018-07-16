<?php

namespace frontend\modules\common_actions;

use frontend\modules\common_actions\BaseAction;
use frontend\models\MicrocicleTrainingManager;

class SetTrainingAsComplete extends BaseAction
{
    public function run()
    {
        if (!\Yii::$app->getRequest()->getIsPost()) { return; }

        $data = json_decode(\Yii::$app->getRequest()->getRawBody(), true);
        $this->model = MicrocicleTrainingManager::className();
        $this->scenario = MicrocicleTrainingManager::TRAINING_MANIPULATION;
        $this->action = 'setTrainingAsComplete';
        $this->params = [
            \Yii::$app->user->identity->getId(),
            $data['MicrocicleTrainingManager']['plans'],
            $data['MicrocicleTrainingManager']['sessionId']
        ];
        return $this->preparedRun();
    }
}