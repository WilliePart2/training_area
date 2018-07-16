<?php

namespace frontend\modules\common_actions;

use frontend\modules\common_actions\BaseAction;
use frontend\models\MicrocicleManager;

class SetMicrocicleAsComplete extends BaseAction
{
    public function run()
    {
        if (!\Yii::$app->getRequest()->getIsPost()) { return false; }

        $data = json_decode(\Yii::$app->getRequest()->getRawBody(), true);
        $this->model = MicrocicleManager::className();
        $this->scenario = MicrocicleManager::MICROCICLE_MANIPULATION;
        $this->params = [
            \Yii::$app->user->identity->getId(),
            $data['MicrocicleManager']['microcicleId']
        ];
        $this->action = 'markMicrocicleAsComplete';
        return $this->preparedRun();
    }
}