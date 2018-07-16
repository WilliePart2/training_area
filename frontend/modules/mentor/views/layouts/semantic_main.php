<?php

use yii\helpers\Html;
use frontend\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->render('_menu') ?>
<?php $this->render('_card') ?>
<?php $this->render('_submenu') ?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= \Yii::$app->language ?>">
<head>
    <?= Html::csrfMetaTags() ?>
    <meta charset="<?= \Yii::$app->charset ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= $this->title ?></title>
    <?php $this->head() ?>
</head>
<body>
<?= $this->beginBody() ?>
<?= isset($this->blocks['menu']) ? $this->blocks['menu'] : '' ?>
<div class="ui stackable centered equal width padded grid">
    <div class="four wide column">
        <?= isset($this->blocks['card']) ? $this->blocks['card'] : '' ?>
        <?= isset($this->blocks['submenu']) ? $this->blocks['submenu'] : '' ?>
    </div>
    <div class="column">
        <div class="ui segment">
            <?= $content ?>
        </div>
    </div>
</div>
<?= $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
