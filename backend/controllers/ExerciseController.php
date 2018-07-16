<?php

namespace backend\controllers;

use backend\models\Exersises;
use backend\models\AddExercises;
use backend\models\GroupsExersises;
use yii\web\Controller;
use Yii;
use yii\helpers\Url;

class ExerciseController extends Controller
{
    const PAGE_SIZE = 3;

    public function actionIndex()
    {
        return $this->render('index');
    }
    /*
     * Считывание списка упражнений
     * offset + limit определяеться в GET параметрах
    */
    public function actionReadExercises()
    {
        $request = Yii::$app->getRequest();
        $model = new Exersises();
        $groupsModel = new GroupsExersises();
        $page = $request->get('page') ? $request->get('page') : 1;
        $group = $request->get('group');
        $offset = self::PAGE_SIZE * $page - self::PAGE_SIZE;

        $exercise = $model->getSomeExercises($offset, self::PAGE_SIZE, $group);

        $notEmptyGroups = $model->getNotEmptyGroups();
        $groups = $groupsModel->getAllGroups();

        return $this->render('exercise-list', [
            'list' => $exercise,
            'pageSize' => self::PAGE_SIZE,
            'countItems' => $model->getCountExercises($group),
            'notEmptyGroups' => $notEmptyGroups,
            'groups' => $groups,
            'page' => $page,
            'group' => $group
        ]);
    }

    /*
     * Добавление упражнения
    */
    public function actionAddExercise()
    {
        $model = new AddExercises();
        $groups = GroupsExersises::find()->all();
        $groupsArray = [];
        foreach($groups as $group){
            $groupsArray[$group->id] = $group->muskul_group;
        }

        if(Yii::$app->request->isPost) {
            $model->scenario = AddExercises::ADD_EXERCISE;
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $result = $model->addExercise();
                if ($result) {
                    return $this->render('exercise-success-add', [
                        'model' => $model,
                        'groups' => $groupsArray
                    ]);
                } else {
                    return $this->render('exercise-deny-add');
                }
            }

            return $this->render('exercise-error-add', [
                'model' => $model,
                'groups' => $groupsArray
            ]);
        }

        return $this->render('exercise-add',[
            'model' => $model,
            'groups' => $groupsArray
        ]);
    }

    /*
     * Удаление упражнения
    */
    public function actionDeleteExercise()
    {

    }

    /*
     * Изменение кпражнения
    */
    public function actionAlterExercise()
    {

    }
}