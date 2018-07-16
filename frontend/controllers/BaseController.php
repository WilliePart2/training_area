<?php

namespace frontend\controllers;

use frontend\models\Users;
use yii\rest\Controller;
use yii\web\Response;
use frontend\modules\user\filters\HttpBearerAuthMod;

class BaseController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_HTML;
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        $behaviors['authenticator']['class'] = HttpBearerAuthMod::className();
        $behaviors['authenticator']['except'] = ['index'];

        return $behaviors;
    }

    public function actionIndex()
    {
        return file_get_contents(\Yii::getAlias('@frontend/web/index.html'));
    }

    public function generateResponse($data = null, $totalCount = null)
    {
        $result = [
            'accessToken' => Users::reNewToken(),
            'data' => $data,
            'result' => $data ? true : false
        ];
        if ($totalCount) {
            $result['totalCount'] = $totalCount;
        }
        return $result;
    }
}