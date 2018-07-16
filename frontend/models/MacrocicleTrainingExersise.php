<?php

namespace frontend\models;

use yii\db\ActiveRecord;

class MacrocicleTrainingExersise extends ActiveRecord
{
    public static function getBaseLayoutByMicrocicleId($id)
    {
        return self::find()->where(['microcicle_id' => $id])->all();
    }
}