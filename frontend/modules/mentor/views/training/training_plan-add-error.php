<?php
if($step === 1) {
    $configForBegin = [
        'model' => $model,
        'step' => $step
    ];
}

if($step === 2) {
    $configForEnd = [
        'name' => $name,
        'step' => $step
    ];
}
?>

<?php $this->beginBlock('error') ?>
 <div class="ui error message">
     Ошибка при добавлении тренировочного плана
 </div>
<?php $this->endBlock('error') ?>

<?= $step === 1 ? $this->render('training_plan-add-begin', $configForBegin) : $this->render('training_plan-add-end', $configForEnd) ?>
