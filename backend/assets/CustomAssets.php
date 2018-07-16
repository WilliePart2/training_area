<?php

namespace backend\assets;

use yii\web\AssetBundle;

class CustomAssets extends AssetBundle
{
    public $sourcePath = '@backend/views';
    public $js = [
        'main.js'
    ];
    public $css = [];
}