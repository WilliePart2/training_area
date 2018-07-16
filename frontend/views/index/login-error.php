<?php $this->beginBlock('error'); ?>
<div class="ui error message">
    Пользователь с такой комбинацией логина и пароля не найден.
</div>
<?php $this->endBlock('error'); ?>

<?= $this->render('login-form', [
    'model' => $model
]); ?>

