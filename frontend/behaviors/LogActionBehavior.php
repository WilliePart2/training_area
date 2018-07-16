<?php

namespace frontend\behaviors;

use yii\base\Behavior;
use frontend\models\MacrocicleActions;

class LogActionBehavior extends Behavior
{
    public function actions()
    {
        return [
            MacrocicleActions::CREATE_TRAINING_PLAN => 'handleMacrocicleAction',
            MacrocicleActions::ALTER_TRAINING_PLAN => 'handleMacrocicleAction',
            MacrocicleActions::ADD_COMMENT_TO_TRAINING_PLAN => 'handleMacrocicleAction',
            MacrocicleActions::SUBSCRIBE_TO_TRAINING_PLAN => 'handleMacrocicleAction'
        ];
    }
    public function handleMacrocicleAction($event)
    {
        echo 'handle training plan action';
        $manager = new MacrocicleActions();
        $manager->createTrainingAction($event->name, $event->initiator, $event->target);
    }
}