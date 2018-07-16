<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class CustomAssets extends AssetBundle
{
    public $sourcePath = '@frontend/views';
    public $js = [
        'main.js'
    ];
    public $css =[
        'main.css'
    ];
}