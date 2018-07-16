<?php

use common\widgets\semantic\SemanticActiveForm;
use yii\helpers\Html;

?>

<div class="ui centered stackable grid">
    <div class="eight wide column">
        <div class="ui segment">

            <?= isset($this->blocks['error']) ? $this->blocks['error'] : '' ?>

            <?php $form = SemanticActiveForm::begin([
                'inputStyle' => 'icon',
                'labelStyle' => 'basic',
                'labelWide' => 'six wide',
                'errorOptions' => [
                    'encode' => false,
                    'class' => 'error-message'
                ]
            ]) ?>

            <?= $form->field($model, 'username', ['placeholder' => 'Имя пользователя']) ?>

            <?= $form->passwordInput($model, 'password', ['placeholder' => 'Введите желаемый пароль']) ?>

            <div class="ui hidden divider"></div>

            <div class="ui inline fields">
                <div class="field">
                    <div class="ui radio checkbox">
                        <input type="radio" name="authType" value="user" checked="checked" />
                        <label>Войти как пользователь</label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui radio checkbox">
                        <input type="radio" name="authType" value="mentor" />
                        <label>Войти как наставник</label>
                    </div>
                </div>
            </div>

            <div class="ui hidden section divider"></div>
            <div class="sixteen wide column">
                <?= Html::submitButton('Войти', [
                    'class' => 'ui basic primary fluid button',
                    'name' => 'loginType',
                    'value' => 'user'
                ]) ?>
            </div>

            <?php SemanticActiveForm::end() ?>
        </div>
    </div>
</div>

