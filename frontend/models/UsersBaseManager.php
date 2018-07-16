<?php

namespace frontend\models;

use yii\base\Model;
use frontend\models\Users;

class UsersBaseManager extends Model
{
    public function findUserByUsernamePart($partOfUsername)
    {
        try {
            $userId = \Yii::$app->user->identity->getId();
            $keySuffix = 'cache_offset';
            $firstChar = strtolower(substr($partOfUsername, 0, 1));
            $data = [];
            $limit = 1000;
            $offset = 0;
            $offsetFromCache =\Yii::$app->cache->get($userId . '_' . $keySuffix . '_' . $firstChar);
            if ($offsetFromCache) {
                $offset = $offsetFromCache;
            }
            $queryResult = Users::findUsersByPartOfName($partOfUsername, $offset, $limit);
            foreach ($queryResult as $userData) {
                if ((int)$userData->id === \Yii::$app->user->identity->getId()) { continue; }
                array_push($data, [
                    'id' => $userData->id,
                    'username' => $userData->username
                ]);
            }
            \Yii::$app->cache->set($userId . '_' . $keySuffix . '_' . $firstChar, (int)$offset + $limit, 60);
            return $data;
        } catch (\Throwable $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }
}