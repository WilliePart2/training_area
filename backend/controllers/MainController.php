<?php

namespace backend\controllers;

use yii\web\Controller;

class MainController extends Controller
{
    public function actionIndex()
    {
        RETURN $this->render('index');
    }
}