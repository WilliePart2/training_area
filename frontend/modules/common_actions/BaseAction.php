<?php

namespace frontend\modules\common_actions;

use yii\base\Action;
use frontend\models\Users;

class BaseAction extends Action
{
    protected $model;
    protected $scenario;
    protected $action;
    protected $params;
    protected $withoutValidate;
    protected function preparedRun()
    {
        if(\Yii::$app->getRequest()->getIsPost()){
            $class = $this->model;
            $model = new $class();
            if (!empty($this->scenario)) { $model->scenario = $this->scenario; }
            $action = $this->action;
            $result = false;
            if (empty($this->withoutValidate)) {
                if ($model->load(json_decode(\Yii::$app->getRequest()->getRawBody(), true)) && $model->validate()) {
                    if (empty($this->params)) {
                        $result = $model->$action();
                    } else {
                        if (is_array($this->params)) {
                            $result = call_user_func_array([$model, $action], $this->params);
                        } else {
                            $result = $model->$action($this->params);
                        }
                    }
                }
            } else {
                if (empty($this->params)) {
                    $result = $model->$action();
                } else {
                    if (is_array($this->params)) {
                        $result = call_user_func_array(array($model, $action), $this->params);
                    } else {
                        $result = $model->$action($this->params);
                    }
                }
            }
            if ($result !== false) {
                /** In this place may be problem */
                return $this->generateResponse($result);
            }
        }
        return $this->generateResponse();
    }
    protected function generateResponse($data = null)
    {
        return [
            'result' => $data === null ? false : true,
            'accessToken' => Users::reNewToken(),
            'data' => $data
        ];
    }
}
