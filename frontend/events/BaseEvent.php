<?php

namespace frontend\events;

use yii\base\Event;

class BaseEvent extends Event
{
    public $initiator;
    public $targetId;
    public $actionType;

    public function __construct($initiator, $target, $config = [])
    {
        $this->initiator = $initiator;
        $this->targetId = $target;
        parent::__construct($config);
    }
}