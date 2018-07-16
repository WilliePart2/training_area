<?php

namespace frontend\models;

use yii\db\ActiveRecord;
use frontend\models\Macrocicle;

class MacrocicleUsers extends ActiveRecord
{
    const CURRENT_MACROCICLE = 1;
    const COMPLETED_MACROCICLE = 2;

    public function getMacrocicle()
    {
        return $this->hasOne(Macrocicle::className(), ['id' => 'macrocicle_id']);
    }
}