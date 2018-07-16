<?php

use yii\helpers\Url;

?>
<?php $this->render('_microcicle_list_template', ['microcicles' => $microcicles]) ?>

<div class="ui stackable centered equal width grid">
    <div class="sixteen wide column">
        <div class="ui dividing header"><?= $plan->name ?></div>
    </div>
    <div class="sixteen wide column">
        <!-- Тут будет список упражнений из шаблона и микроциклов -->
        <div class="ui stackable equal width grid">
            <div class="eight wide column">
                <div class="ui dividing small header">Упражнения составляющие базовый шаблон</div>
                    <?= $this->render('_base_layout_exercises', [
                            'plan' => $plan,
                        'checkbox' => false
                    ]) ?>
            </div>
            <div class="eight wide column">
                <div class="ui dividing small header">Микроциклы входящие в тренировочный план</div>
                <?= isset($this->blocks['microcicles']) ? $this->blocks['microcicles'] : null ?>
            </div>
            <div class="column">
                <div class="ui basic primary fluid button">
                    Редактировать шаблон
                </div>
            </div>
            <div class="column">
                <a href="<?= Url::to(['create-microcicle', 'plan' => $plan->id]) ?>" class="ui aligned bottom basic primary fluid icon button">
                    <i class="ui plus icon"></i>
                    Добавить микроцыкл
                </a>
            </div>
        </div>
    </div>
    <div class="column">
        <!-- Тут будет общий отчет по макроцыклу -->
        <div class="ui dividing centered header">
            Общий отчет по макроциклу
        </div>
    </div>
</div>
