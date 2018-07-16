<?php $this->render('_microcicle_data_parameters') ?>
<?php $this->render('_microcicle_training_name') ?>

<div class="ui equal width stackable grid microcicle_init" data-type="edit" data-id="<?= $microcicle->id ?>" data-name="<?= $microcicle->name ?>">
    <div class="sixteen wide column">
        <div class="ui dividing header"><?= $microcicle->name ?></div>
        <div class="microcicle_messageBox">
            <!-- Тут будут выводиться сообщения -->
        </div>
        <div class="ui horizontal divider">Временные параметры микроцыкла</div>
        <div class="ui equal width stackable grid">
            <?= isset($this->blocks['dateParameters']) ? $this->blocks['dateParameters'] : null ?>
        </div>
    </div>
    <div class="stretched eight wide middle aligned column">
        <?= $this->render('_base_layout_exercises', [
            'plan' => $plans,
            'checkbox' => true
        ]); ?>
    </div>
    <div class="stretched middle aligned column">
        <?= isset($this->blocks['trainingName']) ? $this->blocks['trainingName'] : null ?>
    </div>
</div>
<div class="ui equal width stackable grid">
    <div class="ui sixteen wide column microcicle_trainingRender">
        <!-- Тут будет рендериться информация о тренировках -->
    </div>
    <div class="sixteen wide column">
        <div data-macrocicle-id="<?= $plans->id ?>" class="ui right floated primary basic button microcicle_trainingSave">
            Сохранить микроцикл
        </div>
    </div>
</div>
