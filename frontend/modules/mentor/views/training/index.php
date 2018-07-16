<?php

use yii\helpers\Url;

$counter = 1;

$countPages = ceil($countTrainingPlans / $pageSize);
$pageCounter = 1;
$currentPage = empty($page) ? 1 : intval($page);
?>

<div class="ui left floated header">
    <div class="content">Созданые тренировочные планы</div>
</div>

<a href="<?= Url::to(['create-training-plan-begin']) ?>" class="ui right floated icon basic primary button">
    <i class="ui plus icon"></i>
    Создать тренировочный план
</a>
<div class="ui clearing fitted hidden divider"></div>
<div class="ui fitted divider"></div>
<div class="ui hidden divider"></div>

<div class="ui equal wide stackable centered grid">
    <div class="sixteen wide column">
        <table class="ui unstackable celled inverted selection very compact single line table">
            <thead>
                <tr>
                    <th>Название</th>
                    <th class="collapsing">Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($trainingPlans as $plan): ?>
                    <?php if($pageSize === $counter - 1) break; ?>
                    <tr>
                        <td><?= $plan->name ?></td>
                        <td>
                            <a href="<?= Url::to(['training-plan-edit', 'id' => $plan->id]) ?>" data-content="Редактировать" class="ui inverted tiny basic icon button with-message">
                                    <i class="ui edit icon"></i>
                            </a>
                            <a href="<?= Url::to(['training-plan-delete', 'id' => $plan->id]) ?>" data-content="Удалить" class="ui inverted tiny basic icon button with-message">
                                <i class="ui delete icon"></i>
                            </a>
                        </td>
                    </tr>
                <?php $counter++; endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="sixteen wide column">
        <div class="ui inverted pagination menu">
            <a href="<?= Url::to(['', 'page' => $currentPage - 1]) ?>" class="<?= $currentPage > 1 ? '' : 'disabled' ?> item">
                <i class="ui left chevron icon"></i>
            </a>
            <?php while($countPages >= $pageCounter): ?>
                <a href="<?= Url::to(['', 'page' => $pageCounter]) ?>" class="item <?= $pageCounter === $currentPage ? 'active' : '' ?>"><?= $pageCounter++ ?></a>
            <?php endwhile; ?>
            <a href="<?= Url::to(['', 'page' => $currentPage + 1]) ?>" class="<?= $currentPage < $countPages ? '' : 'disabled' ?> item">
                <i class="ui right chevron icon"></i>
            </a>
        </div>
    </div>
</div>
