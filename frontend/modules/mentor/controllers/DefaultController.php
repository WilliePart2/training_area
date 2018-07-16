<?php

namespace frontend\modules\mentor\controllers;

use yii\web\Controller;
use yii\filters\AccessControl; // Будет фильтровтаь доступ

class DefaultController extends Controller
{
    public function actionIndex()
    {
        return $this->renderFile('@frontend/web/index.html');
    }
}