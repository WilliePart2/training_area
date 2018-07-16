<?php

namespace frontend\modules\common_actions;

use yii\base\Action;
use frontend\models\ExerciseManager;
use frontend\models\Users;

class GetListingExercises extends Action
{
    public function run()
    {
        $model = new ExerciseManager();
        $exercises = $model->getExerciseList();
        if($exercises !== false) {
            return [
                'data' => $model->getExerciseList(),
                'result' => true,
                'accessToken' => Users::reNewToken()
            ];
        }
        return [
            'result' => false,
            'accessToken' => Users::reNewToken()
        ];
    }
}
