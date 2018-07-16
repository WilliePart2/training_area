<?php
namespace frontend\modules\common_actions;

use frontend\modules\common_actions\BaseAction;
use frontend\models\TrainingPlanManager;

class GetSearchResult extends BaseAction
{
    public function run($offset, $limit, $type, $name = null, $category = null)
    {
        $model = new TrainingPlanManager();
        $result = $model->performSearchTrainingPlan($name, $category, $type, $offset, $limit);
        if ($result !== false) {
            return $this->generateResponse($result);
        }
        return $this->generateResponse();
    }
}