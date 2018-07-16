<?php

use yii\helpers\Url;

?>
<?php $this->beginBlock('menu') ?>
<div class="ui fluid menu">
    <a href="<?= Url::to(['main/index']); ?>" class="item">Home</a>
    <div class="ui floating dropdown item">
        Тренировка
        <i class="ui dropdown icon"></i>
        <div class="menu">
            <a href="<?= Url::to(['exercise/read-exercises']) ?>" class="item">Управление упражнениями</a>
            <a href="<?= Url::to(['group-exercise/add-group']) ?>" class="item">Группы упражнений</a>
        </div>
    </div>
</div>
<?php $this->endBlock('menu') ?>
