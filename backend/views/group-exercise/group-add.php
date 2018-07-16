<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\widgets\ActiveField;

$template = <<<TEMPLATE
<div class="ui field">
<div class="ui equal width stackable grid">
    <div class="column">
        {hint}
        <div class="ui labeled fluid input">
            <label class="ui basic secondary label">{label}</label>
            {input}
        </div>
        {error}
    </div>
    <div class="four wide column">
        <button type="submit" class="ui fluid basic primary button">Добавить</button>
    </div>
</div>
</div>
TEMPLATE;


?>

<?php $form = ActiveForm::begin(); ?>

<div class="ui fields">
    <?= new ActiveField([
        'form' => $form,
        'model' => $model,
        'attribute' => 'groupName',
        'template' => $template
    ]) ?>
</div>

<?php ActiveForm::end(); ?>

<?= $this->render('_exists-groups', [
    'groups' => $existsGroups
]) ?>