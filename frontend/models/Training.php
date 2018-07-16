<?php

namespace frontend\models;

use yii\db\ActiveRecord;
use frontend\models\TrainingExersise;
use frontend\models\TrainingExersiseTraining;

class Training extends ActiveRecord
{
    public static function getTrainingByMicrocicleId($id)
    {
        return self::find()->where(['microcicle_id' => $id])->all();
    }
    public static function getTrainingById($id)
    {
        return self::find()->where(['id' => $id])->with('trainingExercises')->one();
    }
    public static function getTrainingByName($trainingName, $microcicleId)
    {
        return self::find()->where(['name' => $trainingName, 'microcicle_id' => $microcicleId])->one();
    }
    public static function getLastInsert($microcicleId, $order = null)
    {
        if(empty($order)) $order = self::getMaxOrder($microcicleId);
        return self::find()->where(['microcicle_id' => $microcicleId, 'order' => $order])->one();
    }
    public static function checkTraining($microcicleId)
    {
        return self::find()->where(['microcicle_id' => $microcicleId])->exists();
    }
    public static function getMaxOrder($microcicleId)
    {
        return self::find()->select('order')->where(['microcicle_id' => $microcicleId])->max('[[order]]');
    }

    /** Метод связывает с таблицей тренировочных упражнений через промежуточную таблицу */
    public function getTrainingExercises()
    {
        return $this->hasMany(TrainingExersise::className(), ['id' => 'training_exersise_id'])
            ->viaTable('training_exersise_training', ['training_id' => 'id']);
    }
    public function getRelationToTrainingExercises()
    {
        return $this->hasMany(TrainingExersiseTraining::className(), ['training_id' => 'id']);
    }
}
