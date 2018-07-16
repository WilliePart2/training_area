<?php

namespace frontend\behaviors;

use yii\base\Behavior;

class MakeActionBehavior extends Behavior
{
    public function createAction($type, $initiator, $target)
    {
        $this->owner->insert(false, [
            'initiator' => $initiator,
            'target_id' => $target,
            'action_type' => $type
        ]);
    }
}