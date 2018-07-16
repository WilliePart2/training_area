<?php
use yii\helpers\Url;
?>


<?php $this->beginBlock('card') ?>
<div class="ui fluid card">
    <div class="ui image">
        <img src="<?= Url::to('willie.png', true) ?>" />
    </div>
    <div class="content">
        <div class="header"><?= \Yii::$app->mentor->identity->username ?></div>
        <div class="meta">
            Administrator
        </div>
        <div class="content"></div>
        <div class="ui divider"></div>
        <div class="extra content">
            <a href="<?= Url::to(['/index/logout']) ?>" class="ui fluid basic button">
                Logout
            </a>
        </div>
    </div>
</div>
<?php $this->endBlock('card') ?>