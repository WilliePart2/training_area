<?php

namespace frontend\models;

use yii\db\ActiveRecord;
use frontend\models\Training;

class Microcicle extends ActiveRecord
{
    public static function findMicrocicleByMacrocicleId($id)
    {
        return self::find()->where(['macrocicle_id' => $id])->all();
    }
    public static function findMicrocicleById($id)
    {
        return self::find()->where(['id' => $id])->with('trainings')->one();
    }
    public static function checkMicrocicle($macrocicleId)
    {
        return self::find()->where(['macrocicle_id' => $macrocicleId])->exists();
    }
    public static function getMaxOrder($macrocicleId)
    {
        return self::find()->where(['macrocicle_id' => intval($macrocicleId)])->max('[[order]]');
    }
    public static function getLastInsert($macrocicleId, $order = null)
    {
        if(empty($order)) $order = self::getMaxOrder($macrocicleId);
        return self::find()->where(['macrocicle_id' => $macrocicleId, 'order' => $order])->one();
    }
    /** Метод связывания с таблицей тренировок */
    public function getTrainings()
    {
        return $this->hasMany(Training::className(), ['microcicle_id' => 'id']);
    }
}