<?php

namespace frontend\modules\mentor\controllers;

use frontend\modules\common_actions\CreateUserPost;
use frontend\modules\common_actions\GetEssentialUserData;
use frontend\modules\common_actions\GetUserPosts;
use frontend\modules\common_actions\HandleVotingForUserListItem;
use frontend\modules\common_actions\IncrementUserPostViewCounter;
use frontend\modules\common_actions\ManageUserPostLikeDislike;
use frontend\modules\common_actions\RemoveUserPost;
use frontend\modules\common_actions\SavePostEdition;
use frontend\modules\common_actions\SetUserRating;
use frontend\modules\common_actions\ToggleFollowingUser;
use frontend\modules\mentor\filters\HttpBearerAuthMod;
use yii\rest\Controller;

class UserPageController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['text/html'] = \yii\web\Response::FORMAT_HTML;
        $behaviors['contentNegotiator']['formats']['application/json'] = \yii\web\Response::FORMAT_JSON;
        $behaviors['authenticator']['class'] = HttpBearerAuthMod::className();
        return $behaviors;
    }
    public function actions()
    {
        return [
            'get-user-posts' => GetUserPosts::className(),
            'get-essential-user-data' => GetEssentialUserData::className(),
            'toggle-following-user' => ToggleFollowingUser::className(),
            'set-user-rating' => SetUserRating::className(),
            'create-user-post' => CreateUserPost::className(),
            'save-post-edition' => SavePostEdition::className(),
            'remove-user-post' => RemoveUserPost::className(),
            'handle-voting-for-user-list-item' => HandleVotingForUserListItem::className(),
            'manage-user-post-like-dislike' => ManageUserPostLikeDislike::className(),
            'increment-user-post-view-counter' => IncrementUserPostViewCounter::className()
        ];
    }

    public function actionMyPage()
    {

    }

    public function actionUserPage()
    {

    }
}