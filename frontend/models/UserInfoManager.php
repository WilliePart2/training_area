<?php
/** Клас выпоняет CRUD операции с информацией о конкретном пользователе */

namespace frontend\models;

use yii\base\Model;

class UserInfoManager extends Model
{
    public function getFieldList($userType)
    {
        $type = 0;
        switch($userType) {
            case 'mentor': $type = 1; break;
            case 'user': $type = 2; break;
        }
        $fieldTable = 'user_contact_fields';
        $valueTable = 'user_contact_values';
        try {
            $result = (new \yii\db\Query())->select(
                'f.id AS identifier, label, group, icon, v.value AS value, v.id AS recordId'
            )
                ->from(['f' => $fieldTable])
                ->leftJoin(['v' => $valueTable], 'f.id=v.field_id')
                ->where(['f.for' => $type])
                ->all();
            $data = [];
            $server = 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'];
            foreach ($result as $item) {
                $tmp = $item;
                if (isset($tmp['icon']) && !empty($tmp['icon'])) {
                    $tmp['icon'] = $server . \Yii::getAlias(\Yii::$app->params['pathToFieldsIcons']) . $tmp['icon'];
                }
                $data[] = $tmp;
            }
            return $data;
        } catch (\Exception $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }
    /**
     * @dataList = {
     *      fieldID: number,
     *      value: any,
     *      userID: number,
     *      isFirstInset: boolean
     * }
     */
    public function setFieldValue($fieldID, $value, $userID, $isFirstInsert, $recordId)
    {
        try {
            if ($isFirstInsert) {
                $result = \Yii::$app->db->createCommand()->insert('user_contact_values', [
                    'field_id' => $fieldID,
                    'users_id' => $userID,
                    'value' => $value
                ])->execute();
            } else {
                $result = \Yii::$app->db->createCommand()->update('user_contact_values', ['value' => $value], [
                    'id' => $recordId
                ])->execute();
            }
            if ($result != false) {
                return true;
            }
            return false;
        } catch (\Throwable $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }
}