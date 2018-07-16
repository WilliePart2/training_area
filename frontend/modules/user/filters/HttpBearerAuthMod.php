<?php

namespace frontend\modules\user\filters;

use yii\filters\auth\HttpBearerAuth;

class HttpBearerAuthMod extends HttpBearerAuth
{
    protected function isActive($action)
    {
        if(strtolower(\Yii::$app->getRequest()->getMethod()) === 'options') {
            return false;
        }
        return parent::isActive($action);
    }
}