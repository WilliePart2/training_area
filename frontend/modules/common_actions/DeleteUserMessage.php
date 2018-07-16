<?php
/**
 * Created by PhpStorm.
 * User: willie
 * Date: 24.06.18
 * Time: 6:20
 */

namespace frontend\modules\common_actions;

use frontend\models\UserCorrespondenceManager;
use frontend\modules\common_actions\BaseAction;

class DeleteUserMessage extends BaseAction
{
    public function run()
    {
        if (!\Yii::$app->getRequest()->getIsPost()) { return; }
        $this->model = UserCorrespondenceManager::className();
        $this->withoutValidate = true;
        $this->action = 'removeMessage';
        $this->params = \json_decode(\Yii::$app->getRequest()->getRawBody(), true);
        return parent::preparedRun();
    }
}