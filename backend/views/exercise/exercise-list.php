<?php

use yii\helpers\Url;

$count = 1;
$pageCount = ceil($countItems / $pageSize);
$pageCounter = 1;
?>

<table class="ui inverted celled selectable unstackable fixed single row very compact table">
    <thead>
        <tr>
            <th class="three wide">№</th>
            <th class="seven wide">Название</th>
            <th class="six wide">Целевая группа</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($list as $item): ?>
            <tr>
                <?=
                '<td>' . $count++ . '</td>' .
                '<td>' . $item->name . '</td>' .
                '<td>' . $item->groupExercise->muskul_group . '</td>'
                ?>
                <?php if($count - 1 === $pageSize) break; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>


<div class="ui inverted pagination menu">
    <?php while($pageCount >= $pageCounter): ?>
        <a class="item <?= $pageCounter === $page ? 'active' : '' ?>" href="<?= Url::to(['exercise/read-exercises', 'page' => $pageCounter, 'group' => empty($group) ? null : $group]) ?>">
            <?= $pageCounter++ ?>
        </a>
    <?php endwhile; ?>
</div>

<?php $this->render('_exercises_category', [
    'notEmptyGroups' => $notEmptyGroups,
    'groups' => $groups
]) ?>