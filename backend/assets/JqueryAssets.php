<?php

namespace backend\assets;

use yii\web\AssetBundle;

class JqueryAssets extends AssetBundle
{
    public $sourcePath = '@bower/jquery/dist';
    public $js = [];
    public $css = [];

    public function init()
    {
        if(YII_ENV_DEV){
            $postfix = '';
        } else {
            $postfix = '.min';
        }

        $this->js[] = 'jquery' . $postfix . '.js';
    }
}