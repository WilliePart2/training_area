<?php $this->render('_microcicle_data_parameters') ?>
<?= $this->render('_microcicle_training_name') ?>

<div class="ui stackable equal width grid microcicle_init" data-type="create">
    <div class="sixteen wide column microcicle_messageBox"></div>
    <div class="sixteen wide column">
        <div class="ui mini header">
            Название микроцикла
        </div>
        <div class="ui fluid input">
            <input class="microcicle_microcicleName" type="text" placeholder="Введите название микроцикла..." />
        </div>
        <div class="ui stackable equal width grid">
            <div class="sixteen wide column">
                <div class="ui horizontal divider">
                    Верменные параметры микроцыкла
                </div>
            </div>
            <?= isset($this->blocks['dateParameters']) ? $this->blocks['dateParameters'] : null ?>
        </div>
    </div>
    <div class="stretched middle aligned column">
    <?= $this->render('_base_layout_exercises', [
        'plan' => $plan,
        'checkbox' => true // Указывает включать ли поле чекбоксов или нет
    ]) ?>
    </div>
    <div class="stretched middle aligned column ui form">
        <?= isset($this->blocks['trainingName']) ? $this->blocks['trainingName'] : null ?>
<!--        <div class="ui mini header">Название тренировки</div>-->
<!--        <div class="ui fluid input">-->
<!--            <input class="microcicle_trainingNameField" type="text" placeholder="Введите название тренировки" />-->
<!--        </div>-->
<!--        <div class="ui divider"></div>-->
<!--        <div class="ui basic primary fluid button microcicle_trainingAddBtn">-->
<!--            Добавить тренировку-->
<!--        </div>-->
    </div>
    <div class="sixteen wide column microcicle_trainingRender">
        <!-- Тут будут рендериться упражнения -->
    </div>
    <div class="sixteen wide column">
        <div data-macrocicle-id="<?= $plan->id ?>" class="ui right floated primary basic button microcicle_trainingSave">
            Сохранить микроцикл
        </div>
    </div>
</div>
