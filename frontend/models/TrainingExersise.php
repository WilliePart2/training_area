<?php

namespace frontend\models;

use yii\db\ActiveRecord;
use frontend\models\Exersises;
use frontend\models\BaseTrainingPlan;

class TrainingExersise extends ActiveRecord
{
    /**
     * Получить список выполняемых тренировочных упражнений по ID тренировки
    */
    public static function getExerciseByBaseExerciseId($id)
    {
        return self::find()->where(['exercise_is' => $id])->all();
    }

    /**
     * Получить выполняемое упражнение по его идентификатору
    */
    Public static function getExerciseById($id)
    {
        return self::findOne($id);
    }

    public static function getLastInsertedExercise($macrocicleId)
    {
        return self::find()->where(['macrocicle_id' => $macrocicleId])->max('[[id]]');
    }
    /**
     * Метод связывает данную таблицу с таблицей упражнений
     */
    public function getExercise()
    {
        return $this->hasOne(Exersises::className(), ['id' => 'exersise_id']);
    }
    /**
     * Метод связывает тренировочное упражнение с раскладками
    */
    public function getPlans()
    {
        return $this->hasMany(BaseTrainingPlan::className(), ['training_exersise_id' => 'id']);
    }
}