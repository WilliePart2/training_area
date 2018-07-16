<?php

use common\widgets\semantic\SemanticActiveForm;
use yii\helpers\Html;

?>

<div class="ui centered stackable grid">
    <div class="eight wide column">
        <?= isset($this->blocks['error']) ? $this->blocks['error'] : '' ?>
        <div class="ui segment">
            <?php $form = SemanticActiveForm::begin([
                'errorOptions' => [
                    'encode' => false,
                    'class' => 'error-message'
                ]
            ]) ?>

                <?= $form->field($model, 'username', ['placeholder' => 'Введите желаемый логин пользователя']) ?>

                <?= $form->emailInput($model, 'email', ['placeholder' => 'Введите ваш email']) ?>

                <?= $form->passwordInput($model, 'password', ['placeholder' => 'Введите пароль']) ?>

                <?= $form->passwordInput($model, 'password_repeat', ['placeholder' => 'Введите пароль еще раз']) ?>

                <div class="ui hidden section divider"></div>
                <div class="sixteen wide column">
                    <?= Html::submitButton('Регистрация', ['class' => 'ui fluid basic primary button']) ?>
                </div>
                <div class="ui hidden divider"></div>

            <?php SemanticActiveForm::end() ?>
        </div>
    </div>
</div>

