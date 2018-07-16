<?php

namespace frontend\controllers;

use frontend\controllers\BaseController;
use frontend\models\UsersBaseManager;

class UserBaseController extends BaseController
{
    /**
     * @param $unm - part of username witch we will use to find needle users records
     * @return array|void
     * @throws \Throwable
     */
    public function actionSearchUserByUsername($unm)
    {
        if (!\Yii::$app->getRequest()->getIsGet()) { return; }
            $model = new UsersBaseManager();
            $result = $model->findUserByUsernamePart($unm);
            if($result) {
                return $this->generateResponse($result);
            }
            return $this->generateResponse();
    }
}