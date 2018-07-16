<?php

use common\widgets\semantic\SemanticActiveForm;
use yii\helpers\Html;

?>

<div class="ui stackable centered grid">

    <?= $this->render('_training_plan-steps', [
            'step' => $step
    ]) ?>

    <div class="sixteen wide column">

        <?= isset($this->blocks['error']) ? $this->blocks['error'] : '' ?>

        <?php $form = SemanticActiveForm::begin() ?>

            <?= $form->field($model, 'name')->label('Название тренировочного плана') ?>

            <?= $form->field($model, 'readme')->textarea()->label('Описание') ?>

            <div class="ui hidden divider"></div>
            <?= Html::submitButton('Создать', ['class' => 'ui basic primary button']) ?>

        <?php SemanticActiveForm::end() ?>
    </div>
</div>
