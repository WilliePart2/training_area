<?php
use yii\helpers\Url;
?>

<?php $this->beginBlock('specific') ?>
<div class="ui four wide column">
    <div>
        <a href="<?= Url::to(['exercise/add-exercise']) ?>" class="ui fluid basic primary button">Добавить упражнение</a>
    </div>
    <div class="ui segment">
        <div class="ui fluid vertical menu">
            <?php foreach($groups as $group): ?>
                <a href="<?= Url::to(['exercise/read-exercises', 'group' => $group->id]) ?>" class="item <?= in_array($group->id, $notEmptyGroups) ? 'link' : 'disabled' ?>"><?= $group->muskul_group ?></a>
            <?php endforeach ?>
        </div>
    </div>
</div>
<?php $this->endBlock('specific') ?>
