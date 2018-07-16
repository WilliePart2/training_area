<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use backend\models\AddExercises;
use backend\models\GroupsExersises;
use yii\helpers\Url;

class GroupExerciseController extends Controller
{
    public function actionIndex()
    {
        $groups = new GroupsExersises();
        return $this->render('index',[
            'count' => $groups->getCountGroups()
        ]);
    }

    /**
     * Добавляет целевую группу для упражнения
    */
    public function actionAddGroup()
    {
        $model = new AddExercises();
        $groups = GroupsExersises::find()->all();
        $groupsArray = [];
        foreach($groups as $group){
            $groupsArray[$group->id] = $group->muskul_group;
        }
        if(Yii::$app->request->isPost) {
            $model->scenario = AddExercises::ADD_GROUP;
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $result = $model->addGroup();
                if($result){
                    return $this->redirect(Url::to('group-exercise/add-group', true));
                } else {
                    return $this->render('group-add-error', [
                        'model' => $model,
                        'existsGroups' => $groupsArray
                    ]);
                }
            }
        }

        return $this->render('group-add', [
            'model' => $model,
            'existsGroups' => $groupsArray
        ]);
    }

    /**
     * Удаляе целевую группу
    */
    public function actionDeleteGroup()
    {
        if(Yii::$app->request->isPost){
            $groupId = Yii::$app->request->post('id');

            $groups = new GroupsExersises();

            $response = Yii::$app->getResponse();
            $response->statusCode = 200;
            $response->format = $response::FORMAT_JSON;
            $response->data = [
                'message' => $groups->deleteGroup($groupId)
            ];

            return $response;
        }
    }
}