<?php
namespace frontend\modules\common_actions;

use frontend\modules\common_actions\BaseAction;
use frontend\models\MicrocicleManager;

class deleteMicrocicleFromTrainingPlan extends BaseAction
{
    public function run()
    {
        if (!\Yii::$app->getRequest()->getIsPost()) { return; }
        $data = \json_decode(\Yii::$app->getRequest()->getRawBody(), true);
        $this->model = MicrocicleManager::className();
        $this->withoutValidate = true;
        $this->action = 'deleteMicrocicleFromTrainingPlan';
        $this->params = $data['MicrocicleManager']['microcicleId'];
        return parent::preparedRun();
    }
}
?>
