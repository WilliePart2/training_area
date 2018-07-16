
<div class="ui success message">
    Упражнение успешно добавлено!
</div>

<?= $this->render('exercise-add',[
    'model' => $model,
    'groups' => $groups
]); ?>