<?php

namespace frontend\modules\user\controllers;

use yii\rest\Controller;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        return $this->renderFile('@frontend\web\index.html');
    }
}