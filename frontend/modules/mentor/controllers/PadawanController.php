<?php

namespace frontend\modules\mentor\controllers;

use frontend\controllers\UserBaseController;
use frontend\models\UsersManager;
use frontend\modules\common_actions\GetUsersList;
use yii\rest\Controller;
use frontend\models\TrainingManager;
use yii\web\Response;
use frontend\modules\mentor\filters\HttpBearerAuthMod;
use yii\filters\AccessControl;
use yii\filters\Cors;
use frontend\models\Users;
use frontend\models\PadawanManager;
use frontend\models\TrainingPlanManager;

class PadawanController extends UserBaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
//        $behaviors['corsFilter'] = [
//            'class' => Cors::className(),
//            'cors' => [
//                'Origin' => ['*'],
//                'Access-Control-Request-Headers' => ['*'],
//                'Access-Control-Request-Method' => ['POST', 'GET', 'PUT', 'DELETE']
//            ]
//        ];
        $behaviors['authenticator']['class'] = HttpBearerAuthMod::className();
//        $behaviors['contentNegotiator'] = [
//            'class' => 'yii\filters\ContentNegotiator',
//            'formats' => [
//                'text/html' => Response::FORMAT_HTML,
//                'application/json' => Response::FORMAT_JSON
//            ]
//        ];
//        $behaviors['accessControl'] = [
//            'class' => AccessControl::className(),
//            'rules' => [
//                [
//                    'allow' => true,
//                    'roles' => ['mentor']
//                ]
//            ]
//        ];
        return $behaviors;
    }

    public function actions()
    {
        return [
            'get-users-list' => GetUsersList::className()
        ];
    }

    public function actionIndex()
    {
        return $this->renderFile('@web/index.html');
    }
    /**
     * Метод возвращает подопечных ментора
    */
    public function actionGetOwnPadawans($offset = 0, $limit = 10)
    {
        if (!\Yii::$app->getRequest()->getIsGet()) { return; }
        $model = new PadawanManager();
        $id = \Yii::$app->user->identity->getId();
        $padawans = $model->getOwnPadawans($id, $offset, $limit);
        /** @var  $padawansCount - deprecated */
//        $padawansCount = $model->getCountOwnPadawans($id);
        /** @var  $relations - deprecated */
//        $relations = $model->getRequestsToRelation(\Yii::$app->user->identity->getId(), $offset, $limit);
//        return $this->generateResponse([
//            'padawans' => $padawans,
//            'fromMentor' => $relations['fromMentor'], /** нужно для фильтрации вывода пользователей */
//            'toMentor' => $relations['toMentor'] /** нужно для фильтрации вывода пользователей */
//        ], [
//            'padawansCount' => $padawansCount
//        ]);
        return $this->generateResponse(['padawans' => $padawans]);
    }
    /**
     * Метод возвращает запросы от пользователей к ментору с заданым смещением
    */
    public function actionGetRequestFromUsers($offset, $limit)
    {
        if (!\Yii::$app->getRequest()->getIsGet()) { return; }
        $model = new TrainingManager();
        $requests = $model->getRequestsToMentor(\Yii::$app->user->identity->getId(), $offset, $limit);
        if(!empty($requests)) {
            return $this->generateResponse($requests['userData']/* $requests['totalCount'] */);
        }
        return $this->generateResponse();
    }
    /**
     * Метод возвращает запросы отправленые от ментора пользователям
    */
    public function actionGetRequestFromMentor($offset, $limit)
    {
        if (!\Yii::$app->getRequest()->getIsPost()) { return; }
        $model = new TrainingManager();
        $requests = $model->getRequestFromMentor(\Yii::$app->user->identity->getId(), $offset, $limit);
        if(!empty($requests)) {
            return $this->generateResponse($requests['userData'], $requests['totalCount']);
        }
        return $this->generateResponse();
    }
    /**
     * Метод возвращает собственных подопечных ментора
    */
    public function actionGetOnlyPadawans($offset, $limit)
    {
        $model = new TrainingManager();
        $id = \Yii::$app->user->identity->getId();
        $padawans = $model->getPadawans($id, $offset, $limit);
        $padawansCount = $model->getCountPadawans($id);
        if(!empty($padawans) && !empty($padawansCount)) {
            return $this->generateResponse($padawans, $padawansCount);
        }
        return $this->generateResponse();
    }

    /**
//     * Метод возвращает всех пользователей с заданым смещением и размером выдачи
//    */
//    public function actionGetUsersList($offset, $limit)
//    {
//        $model = new TrainingManager();
//        return $this->generateResponse($model->getAllUsers($offset, $limit), $model->getCountUsers());
//    }
    /**
     * Метод возвращает пользователей по их логинам
    */
    public function actionSearchUsers($offset, $limit)
    {
        return Users::findUsersList($offset, $limit);
    }
    /**
     * Методы для работы со связями менторов с пользователями
    */
    /**
     * Сетод отправляет запрос на связываение ментора с пользователем
    */
    public function actionBindUser()
    {
        if(\Yii::$app->getRequest()->getIsPost()){
            $model = new UsersManager();
            $model->scenario = UsersManager::BINDING_FROM_MENTOR;
            if($model->load(json_decode(\Yii::$app->getRequest()->getRawBody(), true)) && $model->validate()){
                return [
                    'result' => $model->sendBindingRequestFromMentorToUser(),
                    'accessToken' => Users::reNewToken()
                ];
            }
        }
        return [
            'result' => false,
            'accessToken' => Users::reNewToken()
        ];
    }
    public function actionUnbindUser()
    {
        if(\Yii::$app->getRequest()->getIsPost()){
            /** Обноляем токен доступа */
            $token = Users::reNewToken();

            /** Обрабатываем полученые данные */
            $model = new UsersManager();
            $model->scenario = UsersManager::BINDING_FROM_MENTOR;
            if($model->load(json_decode(\Yii::$app->getRequest()->getRawBody(), true)) && $model->validate()){
                $result = $model->sendUnbindingRequestFromMentorToUser();
                if($result){
                    return [
                        'result' => true,
                        'accessToken' => $token
                    ];
                }
            }
        }
        return [
            'result' => false,
            'accessToken' => $token
        ];
    }
    /** Метод удаляет собственного ученика */
    public function actionRemoveOwnLearner()
    {
        if(\Yii::$app->getRequest()->getIsPost()){
            $model = new UsersManager();
            $model->scenario = UsersManager::BINDING_FROM_MENTOR;
            if($model->load(json_decode(\Yii::$app->getRequest()->getRawBody(), true)) && $model->validate()){
                $result = $model->removeOwnPadawan();
                if(!empty($result)){
                    return $this->generateResponse($result);
                }
            }
        }
        return $this->generateResponse();
    }

    /** Методы ответа пользователям на запросы связывания */
    /** Хэлпер для ответа на запрос */
    private function handlePadawan($answer)
    {
//        $token = Users::reNewToken();
        if(\Yii::$app->getRequest()->getIsPost()){
            $model = new UsersManager();
            $model->scenario = UsersManager::BINDING_FROM_MENTOR;
            if($model->load(json_decode(\Yii::$app->getRequest()->getRawBody(), true)) && $model->validate()) {
                $result = $model->handlePadawanRequest($answer);
                if (!empty($result)) {
                    return $this->generateResponse($result);
                }
            }
        }
        return $this->generateResponse();
    }
    public function actionAcceptPadawan()
    {
        return $this->handlePadawan(1);
    }
    public function actionRejectPadawan()
    {
        return $this->handlePadawan(0);
    }
    public function actionAppointTrainingPlanToPadawan()
    {
        if (\Yii::$app->getRequest()->getIsPost()) {
            $model = new TrainingPlanManager();
            $data = \json_decode(\Yii::$app->getRequest()->getRawBody(), true);
            $model->scenario = TrainingPlanManager::MACROCICLE_MANIPULATION;
            if ($model->load($data) && $model->validate()) {
                $result = $model->setTrainingPlanAsCurrent($data['TrainingPlanManager']['padawanId']);
                if ($result !== false) {
                    return $this->generateResponse($result);
                }
            }
        }
        return $this->generateResponse();
    }

    /** Метод формирует ответ на запрос */
    public function generateResponse($data = null, $totalCount = null)
    {
        return [
            'accessToken' => Users::reNewToken(),
            'result' => empty($data) ? false : true,
            'data' => $data,
            'totalCount' => $totalCount
        ];
    }
}