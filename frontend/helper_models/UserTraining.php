<?php

namespace frontend\helper_models;

use frontend\helper_models\BaseHelperModel;

class UserTraining extends BaseHelperModel
{
    public $completedPlans;
    public $currentPlan;
    public $ownPlans;
    public function init($data)
    {
        $this->completedPlans = $data['completedPlans'];
        $this->currentPlan = $data['currentPlan'];
        $this->ownPlans = $data['ownPlans'];
    }
}