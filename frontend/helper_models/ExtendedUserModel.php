<?php

namespace frontend\helper_models;

use frontend\helper_models\UserViewModel;

class ExtendedUserModel extends UserViewModel
{
    public $mentor;
    public $rating;
    public $trainings;
    public $countPadawans;
    public $completedPlans;
    public $currentPlan;
    public $ownPlans;
    public function init($data) {
        parent::init($data);
        $this->mentor = $data['mentor'];
        $this->rating = $data['rating'];
        $this->trainings = $this->setTraining($data['trainings']);
        $this->countPadawans = $data['countPadawans'];
    }
    private function setTraining($training)
    {
        return new UserTraining($training);
    }
}