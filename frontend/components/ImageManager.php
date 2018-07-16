<?php

namespace frontend\components;

use yii\base\Component;

class ImageManager extends Component
{
    public function getImages()
    {
        $pathForRead = \Yii::getAlias(\Yii::$app->params['pathToCommonIconsDirectoryForRead']);
        $pathForLoad = \Yii::getAlias(\Yii::$app->params['pathToCommonIconsDirectoryForLoad']);
        $dir = opendir($pathForRead);
        $server = 'http://' . $_SERVER['SERVER_NAME'];
        $data = [];
        while (false !== ($row = readdir($dir))) {
            if ($row === '.' || $row === '..') { continue; }
            $data[] = [
                'name' => \preg_replace('~\.(png|jpeg|jpg|svg)~', '', $row),
                'path' => $server . $pathForLoad . '/' . $row
            ];
        }
        return $data ? $data : null;
    }
}