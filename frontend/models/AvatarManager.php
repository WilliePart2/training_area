<?php

namespace frontend\models;

use yii\base\Model;

class AvatarManager extends Model
{
    const SET_AVATAR = 'SET_AVATAR';

    public $url;

    public function rules()
    {
        return [
            ['url', 'required', 'on' => self::SET_AVATAR]
        ];
    }
    public function getAvatarList()
    {
        $pathToDir = \Yii::getAlias('@frontend/web/avatars');
        $avatarsDirectory = @\opendir($pathToDir);
        $data = [];
        if ($avatarsDirectory !== false) {
            while (false !== ($file = \readdir($avatarsDirectory))) {
                if (\preg_match('~.*\.(jpg|jpeg|png|svg)$~', $file)) {
                    $data[] = 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/avatars/' . $file;
                }
            }
        }
        return $data;
    }
    public function setAvatarToUser($userID)
    {
        try {
            $result = \Yii::$app->db->createCommand('UPDATE `users` SET avatar=:url WHERE id=:userID', [
                ':url' => \basename($this->url),
                ':userID' => $userID
            ])->execute();
            if ($result != false) {
                return true;
            }
            return false;
        } catch (\Exception $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }
}