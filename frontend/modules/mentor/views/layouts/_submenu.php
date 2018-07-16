<?php
use yii\helpers\Url;
?>
<?php $this->beginBlock('submenu'); ?>
<div class="ui vertical fluid menu">
    <a href="<?= Url::to(['/mentor/padavans/index']) ?>" class="item">
        Подопечные
    </a>
    <a href="<?= Url::to(['/mentor/training/index']) ?>" class="item">
        Тренировки
    </a>
    <a class="ui disabled item">
        Инструменты
    </a>
    <a class="ui disabled item">
        База знаний
    </a>
</div>
<?php $this->endBlock('submenu') ?>
