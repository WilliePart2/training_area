<?php

namespace backend\models;

use yii\base\Model;
use backend\models\Exersises;
use backend\models\GroupsExersises;

class AddExercises extends Model
{
    public $exerciseName;
    public $exerciseGroup;
    public $groupName;

    const ADD_EXERCISE = 'ADD_EXERCISE';
    const ADD_GROUP = 'ADD_GROUP';

    public function attributeLabels()
    {
        return [
            'exerciseName' => 'Имя упражнения:',
            'exerciseGroup' => 'Целевая группа мышц:',
            'groupName' => 'Имя группы: '
        ];
    }
    public function scenarios()
    {
        return [
            self::ADD_EXERCISE => ['exerciseName', 'exerciseGroup'],
            self::ADD_GROUP => ['groupName']
        ];
    }
    public function rules()
    {
        return [
            [['exerciseName', 'exerciseGroup'], 'required', 'on' => self::ADD_EXERCISE, 'message' => 'Поле должно быть заполнено'],
            [
                'exerciseName',
                'unique',
                'targetClass' => Exersises::className(),
                'targetAttribute' => 'name',
                'on' => self::ADD_EXERCISE,
                'message' => 'упражнение с такми именем уже добавлено'
            ],
            ['groupName', 'required', 'on' => self::ADD_GROUP, 'message' => 'Не заполнено имя группы'],
            [
                'groupName',
                'unique',
                'targetClass' => GroupsExersises::className(),
                'targetAttribute' => 'muskul_group',
                'on' => self::ADD_GROUP
            ]
        ];
    }
    public function addExercise()
    {
        try {
            $exercise = new Exersises();
            $exercise->name = $this->exerciseName;
            $exercise->group_id = $this->exerciseGroup;

            $this->exerciseName = '';
            $this->exerciseGroup = '';

            $exercise->save();
            return true;
        } catch (\Throwable $error){
            // Залогировать ошибку
            return false;
        }
    }
    public function addGroup()
    {
        try {
            $group = new GroupsExersises();
            $group->muskul_group = $this->groupName;
            $this->groupName = '';
            $group->save();
            return true;
        } catch (\Throwable $error){
            // Залогировать ошибку
            return false;
        }
    }
}