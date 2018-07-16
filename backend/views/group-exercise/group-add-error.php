
<div>
    Ошибка при добавлении группы
</div>

<?= $this->render('group-add', [
    'model' => $model,
    'existsGroups' => $existsGroups
]) ?>