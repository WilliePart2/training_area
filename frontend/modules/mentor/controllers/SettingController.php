<?php

namespace frontend\modules\mentor\controllers;

use frontend\modules\common_actions\GetAvailableAvatars;
use frontend\modules\common_actions\GetFieldList;
use frontend\modules\common_actions\SetAvatarAsCurrent;
use frontend\modules\common_actions\SetUserInfoFieldValue;
use frontend\modules\user\filters\HttpBearerAuthMod;
use yii\rest\Controller;

class SettingController extends Controller
{
    public function actions()
    {
        return [
            'get-available-avatars' => GetAvailableAvatars::className(),
            'set-avatar-as-current' => SetAvatarAsCurrent::className(),
            'get-available-fields-list' => GetFieldList::className(),
            'set-user-info-field' => SetUserInfoFieldValue::className()
        ];
    }
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['class'] = HttpBearerAuthMod::className();
        $behaviors['contentNegotiator']['formats']['text/html'] = \yii\web\Response::FORMAT_HTML;
        $behaviors['contentNegotiator']['formats']['application/json'] = \yii\web\Response::FORMAT_JSON;
        return $behaviors;
    }
    public function actionIndex()
    {
        return $this->renderFile('@frontend/web/index.html');
    }
}