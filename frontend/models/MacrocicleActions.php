<?php

namespace frontend\models;

use frontend\behaviors\MakeActionBehavior;
use yii\db\ActiveRecord;

class MacrocicleActions extends ActiveRecord
{
    const CREATE_TRAINING_PLAN = 'CREATE_TRAINING_PLAN';
    const ALTER_TRAINING_PLAN = 'ALTER_TRAINING_PLAN';
    const ADD_COMMENT_TO_TRAINING_PLAN = 'ADD_COMMENT_TO_TRAINING_PLAN';
    const SUBSCRIBE_TO_TRAINING_PLAN = 'SUBSCRIBE_TO_TRAINING_PLAN';

    public function behaviors()
    {
        return [
            'makeActionBehavior' => MakeActionBehavior::className()
        ];
    }

    public function createTrainingAction($type, $initUserId, $targetId)
    {
        switch ($type) {
            case self::CREATE_TRAINING_PLAN: $this->actionCreate($initUserId, $targetId); break;
            case self::ALTER_TRAINING_PLAN: $this->actionAlter($initUserId, $targetId); break;
            case self::ADD_COMMENT_TO_TRAINING_PLAN: $this->actionAddComment($initUserId, $targetId); break;
            case self::SUBSCRIBE_TO_TRAINING_PLAN: $this->actionSubscribe($initUserId, $targetId); break;
        }
    }

    /**
     * @param $initUserId - user which create training plan
     * @param $targetId - training plan which bin created
     * @return boolean
     * @throws
     */
    public function actionCreate($initUserId, $targetId)
    {
        try {
            self::insert(false ,[
                'initiator' => $initUserId,
                'target_id' => $targetId,
                'action_type' => self::CREATE_TRAINING_PLAN
            ]);
        } catch (\Throwable $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    /**
     * @param $initUserId - user which altered training plan
     * @param $targetId - training plan which bin altered
     * @return boolean
     * @throws
     */
    public function actionAlter($initUserId, $targetId)
    {
        try {
            self::insert(false, [
                'initiator' => $initUserId,
                'target_id' => $targetId,
                'action_type' => self::ALTER_TRAINING_PLAN
            ]);
        } catch (\Throwable $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    /**
     * @param $initUserId - user which added comment
     * @param $targetId - training plan for which added comment
     * @return boolean
     * @throws
     */
    public function actionAddComment($initUserId, $targetId)
    {
        try {
            $this->createAction(self::ADD_COMMENT_TO_TRAINING_PLAN, $initUserId, $targetId);
        } catch (\Throwable $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    /**
     * @param $initUserId - user which make subscription on training plan
     * @param $targetId - training plan on which subscribe
     * @return boolean
     * @throws
     */
    public function actionSubscribe($initUserId, $targetId)
    {
        try {
            $this->createAction(self::SUBSCRIBE_TO_TRAINING_PLAN, $initUserId, $targetId);
        } catch (\Throwable $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }
}