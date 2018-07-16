<?php

namespace frontend\modules\mentor\controllers;

use frontend\modules\common_actions\CreateUserChatRoom;
use frontend\modules\common_actions\DeleteChatRoom;
use frontend\modules\common_actions\GetUserMessages;
use frontend\modules\common_actions\GetUserMessagesAddressees;
use frontend\modules\common_actions\GetUsersCorrespondences;
use frontend\modules\common_actions\SendMessageToUser;
use frontend\modules\common_actions\SetMessagesAsConsidered;
use yii\rest\Controller;
use frontend\modules\mentor\filters\HttpBearerAuthMod;
use frontend\modules\common_actions\DeleteUserMessage;

class MessageController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['class'] = HttpBearerAuthMod::className();
        $behaviors['authenticator']['except'] = ['index'];
        $behaviors['contentNegotiator']['formats']['text/html'] = \yii\web\Response::FORMAT_HTML;
        $behaviors['contentNegotiator']['formats']['application/json'] = \yii\web\Response::FORMAT_JSON;
        return $behaviors;
    }

    public function actions()
    {
        return [
            'create-user-chat-room' => CreateUserChatRoom::className(),
            'get-user-messages' => GetUserMessages::className(),
            'send-message' => SendMessageToUser::className(),
            'delete-user-message' => DeleteUserMessage::className(),
            'delete-chat-room' => DeleteChatRoom::className(),
            'set-messages-as-considered' => SetMessagesAsConsidered::className(),
            'get-user-correspondences' => GetUsersCorrespondences::className(),
            'get-user-messages-addressees' => GetUserMessagesAddressees::className()
        ];
    }

    public function actionIndex()
    {
        return file_get_contents(\Yii::getAlias('@frontend/web/index.html'));
    }
}