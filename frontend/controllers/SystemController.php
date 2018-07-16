<?php

namespace frontend\controllers;

use yii\rest\Controller;

class SystemController extends Controller
{
    public function actionGetImageSet()
    {
        return \Yii::$app->imageManager->getImages();
    }
}