<?php

use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;
use yii\helpers\Html;

$template = <<<TEMPLATE
<div class="ui labeled fluid input">
    {hint}
    <div class="ui basic label">{label}</div>
    {input}
    {error}
</div>
TEMPLATE;

?>

<?php $form = ActiveForm::begin(); ?>

<div class="ui equal width stackable grid">
    <div class="ui ten wide column">
            <?= new ActiveField([
                'form' => $form,
                'model' => $model,
                'attribute' => 'exerciseName',
                'template' => $template
            ])  ?>
    </div>
    <div class="column">
        <div class="ui equal width stackable grid">
            <div class="ten wide column">
                <?= Html::activeDropDownList($model, 'exerciseGroup', $groups, ['class' => 'ui fluid dropdown']) ?>
            </div>
            <div class="column">
                <?= Html::submitButton('Добавить', ['class' => 'ui fluid basic primary button']) ?>
            </div>
        </div>
    </div>

</div>

<?php ActiveForm::end(); ?>
