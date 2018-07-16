<?php

use yii\helpers\Html;
use backend\assets\AppAsset;
//use Yii;

AppAsset::register($this);
?>
<?php $this->render('_menu') ?>
<?= $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>" />
    <?= Html::csrfMetaTags() ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width" />
    <title><?= $this->title ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="ui equal width centered padded stackable grid">
    <?= $this->blocks['menu'] ?>
    <?= isset($this->blocks['specific']) ? $this->blocks['specific'] : '' ?>
    <div class="ui column">
        <div class="ui segment">
            <?= $content ?>
        </div>
    </div>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?= $this->endPage() ?>
