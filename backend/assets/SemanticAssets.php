<?php

namespace backend\assets;

use yii\web\AssetBundle;

class SemanticAssets extends AssetBundle
{
    public $sourcePath = '@bower/semantic-ui/dist';
    public $js = [];
    public $css = [];

    public function init()
    {
        if(!YII_ENV_DEV){
            $postfix = '.min';
        } else {
            $postfix = '';
        }

        $this->js[] = 'semantic' . $postfix . '.js';
        $this->css[] = 'semantic' . $postfix . '.css';
        parent::init();
    }
}