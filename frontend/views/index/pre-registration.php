<?php

use yii\helpers\Url;

?>

<div class="ui centered stackable grid">
    <div class="eight wide column">
        <div class="ui segment">
            <a href="<?= Url::to(['index/user-registration']) ?>" class="ui fluid large basic primary button">
                Зарегистрироваться как пользователь
            </a>
            <div class="ui hidden divider"></div>
            <a href="<?= Url::to(['index/mentor-registration']) ?>" class="ui fluid large basic red button">
                Зарегистрироваться как наставник
            </a>
        </div>
    </div>
</div>
