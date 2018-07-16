<?php

use frontend\assets\AppAsset;
use yii\helpers\Html;

AppAsset::register($this);
?>
<?php $this->render('_menu'); ?>

<?= $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= \Yii::$app->language ?>">
<head>
    <meta charset="<?= \Yii::$app->charset ?>" />
    <?= Html::csrfMetaTags() ?>
    <title><?= $this->title ?></title>
    <?php $this->head() ?>
</head>
<body>
<?= $this->blocks['menu'] ?>
<?= $this->beginBody() ?>
    <?= $content ?>
<?= $this->endBody() ?>
</body>
</html>
<?= $this->endPage() ?>
