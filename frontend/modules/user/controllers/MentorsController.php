<?php

namespace frontend\modules\user\controllers;

use frontend\models\TrainingManager;
use frontend\modules\common_actions\GetUsersList;
use frontend\modules\user\User;
use yii\rest\Controller;
use frontend\models\UsersManager;
use frontend\models\Users;
use frontend\modules\user\filters\HttpBearerAuthMod;
use yii\web\Response;

class MentorsController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuthMod::className()
        ];
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_HTML;
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        return $behaviors;
    }
    public function actions()
    {
        return [
            'get-users-list' => GetUsersList::className()
        ];
    }
    /**
     * Метод возвращает текущего ментора и всю информацию связаную с ним
    */
    public function actionGetMentor()
    {
        if(\Yii::$app->getRequest()->getIsPost()){
            $modelUsers = new UsersManager();
            $modelTrainings = new TrainingManager();
            $data = $modelUsers->getCurrentMentor(\Yii::$app->user->identity->getId());
            $requestToMentor = $modelUsers->getRequestToMentor(\Yii::$app->user->identity->getId());
//            $trainingPlan = $modelTrainings->trainingPlanFromMentor(\Yii::$app->user->identity->getId());
//            if(!empty($data) ||
            if($data) {
                return $this->generateResponse([
                    'currentMentor' => $data,
                    'requestToMentor' => $requestToMentor
                ]);
            }
        }
        return $this->generateResponse();
    }

    /** Метод возвращает листинг пользователей с заданым размером и смещением */
    public function actionGetAllUsers($offset, $limit)
    {
        if(\Yii::$app->getRequest()->getIsGet()){
            $model = new UsersManager();
            $result = $model->getAllUsers($offset, $limit);
            if(!empty($result)){
                return $this->generateResponse($result);
            }
        }
        return $this->generateResponse();
    }

    /**
     * Метод возвращает менторов которые отправили запрос пользователю
     * amend
     */
    public function actionGetRequestFromMentor($offset, $limit)
    {
        if(\Yii::$app->getRequest()->getIsPost()){
            $model = new UsersManager();
            $response = $model->getRequestFromMentor(\Yii::$app->user->getId(), $offset, $limit);
            if(!empty($response)){
                return $this->generateResponse($response /* $response['totalCount'] */);
            }
        }
        return $this->generateResponse();
    }

    /**
     * Обработчик ответа ментору от пользователя
     * amend!
     */
    public function fromMentorRequestHandler($answer)
    {
        if(\Yii::$app->getRequest()->getIsPost()){
            $model = new UsersManager();
            $model->scenario = UsersManager::BINDING_FROM_MENTOR;
            if($model->load(json_decode(\Yii::$app->getRequest()->getRawBody(), true)) && $model->validate()){
                $result = $model->requestFromMentorHandler($answer);
                if(!empty($result)){
                    return $this->generateResponse($result);
                }
            }
        }
        return $this->generateResponse();
    }
    /** Метод подтверждает связь с ментором */
    public function actionAcceptOffer()
    {
        return $this->fromMentorRequestHandler(true);
    }
    /** Метод отклоняет связь с ментором */
    public function actionRejectOffer()
    {
        return $this->fromMentorRequestHandler(false);
    }

    /** Запросы привязки отвязки пользователя к ментору */
    /** Метод отправляет запрос ментору на связывание */
    public function actionBindMentor()
    {
        if(\Yii::$app->getRequest()->getIsPost()){
            $model = new UsersManager();
            $model->scenario = UsersManager::BINDING_FROM_MENTOR;
            if($model->load(json_decode(\Yii::$app->getRequest()->getRawBody(), true)) && $model->validate()) {
                $result = $model->sendBindingRequestFromUserToMentor();
                if(!empty($result)){
                    return $this->generateResponse($result);
                }
            }
        }
        return $this->generateResponse();
    }

    /** Метод отвязывает пользователя от ментора с указаным статусом связи */
    private function handlerUnbindMentor($status)
    {
        if(\Yii::$app->getRequest()->getIsPost()){
            $model = new UsersManager();
            $model->scenario = UsersManager::BINDING_FROM_MENTOR;
            if($model->load(\json_decode(\Yii::$app->getRequest()->getRawBody(), true)) && $model->validate()){
                $result = $model->handleUnbindMentor($status);
                if(!empty($result)){
                    return $this->generateResponse(true);
                }
            }
        }
        return $this->generateResponse();
    }
    /** Отвязываем текущего ментора от пользователя */
    public function actionUnbindMentor()
    {
        return $this->handlerUnbindMentor(1);
    }
    /** Метод отменяет запрос к ментору на связывание */
    public function actionResetRequestToMentor()
    {
        return $this->handlerUnbindMentor(2);
    }

    /** Хэлперы */
    private function generateResponse($data = null, $totalCount = null)
    {
        return [
            'accessToken' => Users::reNewToken(),
            'result' => !empty($data) ? true : false,
            'data' => $data,
            'totalCount' => $totalCount
        ];
    }
}