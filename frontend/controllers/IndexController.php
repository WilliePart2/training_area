<?php

namespace frontend\controllers;

use common\models\User;
use yii\web\Response;
use frontend\models\UsersManager;
use yii\filters\Cors;
use yii\rest\Controller;
use Yii;

class IndexController extends Controller
{
    const USER_ALREADY_EXISTS = 'USER_ALREADY_EXISTS';
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors = \yii\helpers\ArrayHelper::merge([
            'corsFilter' => [
                'class' => Cors::className(),
                'cors' => [
                    'Origin' => ['*'],
                    'Access-Control-Allow-Origin' => ['*'],
                    'Access-Control-Request-Method' => ['*'],
                    'Access-Control-Request-Headers' => ['*']
                ]
            ],
            'contentNegotiator' => [
                'class' => 'yii\filters\ContentNegotiator',
                'formats' => [
                    'text/html' => Response::FORMAT_HTML,
                    'application/json' => Response::FORMAT_JSON
                ]
            ]
        ], $behaviors);
        return $behaviors;
    }
    public function actionIndex()
    {
        return $this->renderFile('@frontend/web/index.html');
    }
    public function actionLogin()
    {
        return $this->renderFile('@frontend/web/index.html');
    }
    public function actionUserLogin()
    {
        $model = new UsersManager();
        $model->scenario = UsersManager::AUTHORIZATION;
        if(Yii::$app->getRequest()->getIsPost()){
            if($model->load(json_decode(Yii::$app->getRequest()->getRawBody(), true)) && $model->validate()){
                $result = $model->loginUser();
                if(!empty($result)) {
                    return $this->createResponseData($result);
                }
            }
        }
        return $this->createResponseData();
    }
    public function actionMentorLogin()
    {
        $model = new UsersManager();
        $model->scenario = UsersManager::AUTHORIZATION;
        if(\Yii::$app->getRequest()->getIsPost()){
            if($model->load(json_decode(\Yii::$app->request->getRawBody(), true)) && $model->validate()){
                $result = $model->loginMentor();
                if(!empty($result)) {
                    return $this->createResponseData($result);
                }
            }
        }
        return $this->createResponseData();
    }
    public function actionRegistration()
    {
        return $this->renderFile('@frontend/web/index.html');
    }
    public function actionUserRegistration()
    {
        $model = new UsersManager();
        $model->scenario = UsersManager::USER_REGISTRATION;
        $request = \Yii::$app->getRequest();
        if($request->getIsPost()) {
            if ($model->load(json_decode($request->getRawBody(), true)) && $model->validate()) {
                $result = $model->registerUser();
                if(!empty($result)){
                    return $this->createResponseData($result, $model, 'user');
                }
            } else {
                return $this->userAlreadyExists();
            }
        }
        return $this->createResponseData();
    }

    public function actionMentorRegistration()
    {
        $model = new UsersManager();
        $model->scenario = UsersManager::MENTOR_REGISTRATION;
        $request = \Yii::$app->getRequest();
        if($request->getIsPost()){
            if($model->load(json_decode($request->getRawBody(), true)) && $model->validate()){
                $result = $model->registerMentor();
                if(!empty($result)) {
                    return $this->createResponseData($result, $model->username, 'mentor');
                };
            } else {
                return $this->userAlreadyExists();
            }
        }
        return $this->createResponseData();
    }

    /**
     * @at - accessToken
    */
    public function actionLogout($at)
    {
        \Yii::$app->cache->delete($at);
        return true;
    }

    private function createResponseData($accessToken = null, $username = null, $type = null)
    {
        if(empty($accessToken)){
            return [
                'result' => false,
                'accessToken' => '',
                'id' => '',
                'username' => '',
                'type' => ''
            ];
        }

        return [
            'result' => true,
            'accessToken' => $accessToken,
            'id' => \Yii::$app->user->identity->getId(),
            'username' => \Yii::$app->user->identity->username,
            'type' => \Yii::$app->user->identity->type,
            'avatar' => \Yii::$app->user->identity->getAvatar()
        ];
    }
    private function userAlreadyExists()
    {
        return [
            'data' => self::USER_ALREADY_EXISTS,
            'result' => true
        ];
    }
}