<?php ?>
<?php $this->beginBlock('trainingName') ?>
<div class="ui mini header">Название тренировки</div>
<div class="ui fluid input">
    <input class="microcicle_trainingNameField" type="text" placeholder="Введите название тренировки" />
</div>
<div class="ui divider"></div>
<div>
    <div class="ui basic primary fluid button microcicle_trainingAddBtn">
        Добавить новую тренировку
    </div>
    <div class="ui hidden fitted divider"></div>
    <div class="ui basic red fluid floating dropdown button microcicle_trainingEditBtn">
        Дополнить существующую тренировку
        <i class="ui dropdown icon"></i>
        <div class="menu">
            <!-- Тут будет список зарегистрированных тренировок -->
        </div>
    </div>
    <div class="ui modal" id="registeredTrainingList">
        <!-- Тут будет рендериться список зарегестрированных тренировок -->
    </div>
</div>
<?php $this->endBlock('trainingName') ?>
