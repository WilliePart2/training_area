<?php

namespace frontend\models;

use yii\base\Model;
use frontend\models\MacrocicleComments;
use frontend\models\MacrocicleCommentsAddingText;
use frontend\models\MacrocicleUserLike;
use frontend\models\MacrocicleUserDislike;
use frontend\models\MacrocicleActions;
use frontend\events\MacrocicleEvent;

class CommentManager extends Model
{
    /* scenarios */
    const MANAGE_COMMENTS = 'MANAGE_COMMENTS';
    const CREATE_COMMENTS = 'CREATE_COMMENTS';
    const GET_COMMENTS = 'GET_COMMENTS';
    const MODIFY_COMMENTS = 'MODIFY_COMMENTS';

    /* actions */
    const ADD_COMMENT_FOR_TRAINING_PLAN = MacrocicleActions::ADD_COMMENT_TO_TRAINING_PLAN;

    public $commentId;
    public $planId;
    public $text;
    public $addingText;

    public function rules()
    {
        return [
            [['planId', 'text'], 'required', 'on' => self::CREATE_COMMENTS],
            ['planId', 'required', 'on' => self::GET_COMMENTS],
            ['commentId', 'required', 'on' => self::MANAGE_COMMENTS],
            [['addingText', 'commentId'], 'required', 'on' => self::MODIFY_COMMENTS]
        ];
    }

    /** Метод возвращает коментарии для микроцикла */
    public function getComments($offset, $limit)
    {
        try {
            $commentKey = \Yii::$app->params['trainingCommentParams']['training_plan_comment_cache_key'];
            $commentsFetchLimit = \Yii::$app->params['trainingCommentParams']['limit_fetching_comments_from_db'];
            $comments = \Yii::$app->cache->get($commentKey);

            if (!$comments) {
                $comments = MacrocicleComments::find()
                    ->where([
                        'macrocicle_id' => $this->planId
                    ])
                    ->offset($offset)
                    ->limit($commentsFetchLimit)
                    ->orderBy(['[[id]]' => SORT_DESC])
                    ->all();
            }

            if (empty($comments)) return false;

            $parts = array_chunk($comments, $limit);
            $result = array_shift($parts);

            $parts = count($parts) > 2 ? array_reduce($parts, function($store, $item) {
                array_merge($store, $item);
                return $store;
            }, []) : null;
            \Yii::$app->cache->set($commentKey, $parts);

            return [
                'comments' => $this->formatResponse($result),
                'totalCount' => MacrocicleComments::find()->where(['macrocicle_id' => $this->planId])->count()
            ];
        } catch (\Throwable $error) {
            if(YII_ENV_DEV){
                throw $error;
            }
            return false;
        }
    }
    /** Метод создает коментарий */
    public function createComment()
    {
        try {
            $comment = new MacrocicleComments();
            $comment->macrocicle_id = $this->planId;
            $comment->users_id = \Yii::$app->user->identity->getId();
            $comment->text = $this->text;
            $comment->date = gmdate('Y-m-d H:i:s', time());
            $comment->like = 0;
            $comment->dislike = 0;
            $comment->save();

            $this->trigger(
                self::ADD_COMMENT_FOR_TRAINING_PLAN,
                new MacrocicleEvent(\Yii::$app->user->identity->getId(), $comment->id)
            );
            return $this->formatResponse($comment);
        } catch(\Throwable $error) {
            if(YII_ENV_DEV){
                throw $error;
            }
            return false;
        }
    }
    /** Метод изменяет коментраий(удалить данные нельзя, только добавить) */
    public function modifyComment()
    {
        try {
            /** Проверяем может ли пользователь выполнять операцию */
            $comment = MacrocicleComments::findOne($this->commentId);
            if(!empty($comment) && $comment->users_id !== \Yii::$app->user->identity->getId()){
                throw new \Exception("User doesn't have permission to modify this comment");
            }

            /** Добовляем дополнительный текст для коментария */
            $modifyText = new MacrocicleCommentsAddingText();
            $modifyText->comment_id = $this->commentId;
            $modifyText->text = $this->addingText;
            $modifyText->date = date('Y-m-d H:i:s',time());
            $modifyText->save();
            return $this->_formatModificationText($modifyText);
        } catch (\Throwable $error) {
            if(YII_ENV_DEV){
                throw $error;
            }
            return false;
        }
    }
    /** Метод удаляет коментарий */
    public function deleteComment()
    {
        try {
            $comment = MacrocicleComments::find()->where(['id' => $this->commentId])->one();
            if (empty($comment)) return false;
            if ($comment->users_id == \Yii::$app->user->identity->getId()) {
                $comment->delete();
                return true;
            }
            return false;
        } catch(\Throwable $error) {
            if(YII_ENV_DEV){
                throw $error;
            }
            return false;
        }
    }

    /** Управление like/dislike */
    private function likeManager($property, $operation)
    {
        try {
            $transaction = \Yii::$app->getDb()->beginTransaction();

            $comment = MacrocicleComments::findOne($this->commentId);
            $hasVote = ('frontend\models\MacrocicleUser' . ucfirst($property))::find()->where([
                'comment_id' => $this->commentId,
                'user_id' => \Yii::$app->user->identity->getId()
            ])->exists();
            if (!empty($comment)) {
                if($operation) {
                    if(!$hasVote) {
                        $comment->$property = intval($comment->$property) + 1;
                        $this->_addVote($property);
                    } else {
                        return false;
                    }
                } else {
                    if($hasVote) {
                        $comment->$property = intval($comment->$property) - 1;
                        $this->_removeVote($property);
                    } else {
                        return false;
                    }
                }
                $comment->save();
            }

            $transaction->commit();
            return true;
        } catch (\Throwable $error) {
            $transaction->rollBack();
            if(YII_ENV_DEV){
                throw $error;
            }
            return false;
        }
    }
    private function _addVote($type)
    {
        try {
            $className = 'frontend\models\MacrocicleUser' . ucfirst($type);
            $vote = new $className();
            $vote->comment_id = $this->commentId;
            $vote->user_id = \Yii::$app->user->identity->getId();
            $vote->save();
            return true;
        } catch (\Throwable $error) {
            if(YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }
    private function _removeVote($type)
    {
        try{
            $vote = ('frontend\models\MacrocicleUser' . ucfirst($type))::find()->where([
                'comment_id' => $this->commentId,
                'user_id' => \Yii::$app->user->identity->getId()
            ])->one();
            if(empty($vote)){
                throw new \Exception('User vote not found');
            }
            $vote->delete();
            return true;
        } catch (\Throwable $error) {
            if(YII_ENV_DEV){
                throw $error;
            }
            return false;
        }
    }
    public function addLike()
    {
        return $this->likeManager('like', true);
    }
    public function deleteLike()
    {
        return $this->likeManager('like', false);
    }
    public function addDislike()
    {
        return $this->likeManager('dislike', true);
    }
    public function deleteDislike()
    {
        return $this->likeManager('dislike', false);
    }

    /** Хэлперы */
    public function formatResponse($commentData)
    {
        if(!is_array($commentData) && !($commentData instanceof \yii\db\ActiveRecord)) throw new \Exception('Invalid argument for formatResponse');
        if(is_array($commentData)){
            $data = [];
            foreach($commentData as $comment){
                $data[] = $this->_format($comment);
            }
            return $data;
        }
        return $this->_format($commentData);
    }
    private function _format($commentData)
    {
        $addingText = [];
        $modificationText = $commentData->addingText;
        if(!empty($modificationText)){
            foreach($modificationText as $mText) {
                $addingText[] = $this->_formatModificationText($mText);
            }
        }

        $result = [
            'id' => $commentData->id,
            'user' => [
                'id' => $commentData->user->id,
                'username' => $commentData->user->username,
                'avatar' => $commentData->user->getAvatar()
            ],
            'text' => $commentData->text,
            'addingText' => $addingText,
            'date' => $commentData->date,
            'like' => $commentData->like,
            'dislike' => $commentData->dislike,
        ];
        if(\Yii::$app->user->identity) {
            $result['hasLike'] = MacrocicleUserLike::find()->where([
                'comment_id' => $commentData->id,
                'user_id' => \Yii::$app->user->identity->getId()
            ])->exists();
            $result['hasDislike'] = MacrocicleUserDislike::find()->where([
                'comment_id' => $commentData->id,
                'user_id' => \Yii::$app->user->identity->getId()
            ])->exists();
        }
        return $result;
    }
    private function _formatModificationText($data)
    {
        return [
            'id' => $data->id,
            'comment_id' => $data->comment_id,
            'text' => $data->text,
            'date' => $data->date
        ];
    }
}