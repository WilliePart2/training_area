<?php

use yii\helpers\Url;

$auth = \Yii::$app->getAuthManager();
$mentor = $auth->getRole('mentor');

?>

<?php $this->beginBlock('menu') ?>

<div class="ui fluid text menu">
    <div class="link item">
        Home
    </div>
    <?php if(!\Yii::$app->user->isGuest) ?>
        <?= \Yii::$app->user->can($mentor) ? $this->render('_mentors-submenu') : $this->render('_user-submenu') ?>
    <?php ?>
    <div class="right menu item">
        <?= \Yii::$app->user->isGuest && \Yii::$app->mentor->isGuest ? $this->render('_login-buttons') : $this->render('_logout-button') ?>
    </div>
</div>
<div class="ui divider"></div>

<?php $this->endBlock('menu') ?>
