<?php

use yii\helpers\Url;

?>

<div class="ui buttons">
    <a href="<?= Url::to(['index/login']) ?>" class="ui primary button">Вход</a>
    <div class="or" data-text="<->"></div>
    <a href="<?= Url::to(['index/registration']) ?>" class="ui secondary button">Регистрация</a>
</div>

