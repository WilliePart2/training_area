<?php
use yii\helpers\Html;
$counter = 1;
?>

<table class="ui fixed single line unstackable selectable very compact inverted table">
    <thead>
        <tr>
            <th class="three wide">№</th>
            <th>Существующие группы</th>
            <th class="four wide">Действия</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($groups as $groupId => $groupName): ?>
            <?= '<tr><td>'. $counter++ . '</td><td>' . Html::encode($groupName) . '</td><td><div class="delete_group ui inverted very compact white button" data-id="' . $groupId . '">Удалить</div>' . '</td></tr>'?>
        <?php endforeach; ?>
    </tbody>
</table>
