<?php

namespace frontend\modules\mentor;

use yii\base\Module;

class Mentor extends Module
{
    public function init()
    {
        $params = parent::init();
        $params['defaultRoute'] = 'default/index';
        $params['controllerNamespace'] = 'frontend\modules\mentor\controllers';
        $params['layout'] = 'semantic_main';
        $params['components']['user'] = [
            'class' => 'yii\web\User',
            'identityClass' => 'frontend\models\Users',
            'enableSession' => false
        ];
        return $params;
    }
}