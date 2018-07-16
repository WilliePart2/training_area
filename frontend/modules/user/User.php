<?php

namespace frontend\modules\user;

use yii\base\Module;

class User extends Module
{
    public function init()
    {
        $params = parent::init();
        $params['controllerNamespace'] = 'frontend\modules\user\controllers';
        return $params;
    }
}