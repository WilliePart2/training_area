<?php
use yii\helpers\Url;
?>

<?php $this->beginBlock('microcicles') ?>

<div class="ui link selection list">
    <?php foreach($microcicles as $microcicle): ?>
        <div data-microcicle-id="<?= $microcicle->id ?>"
             data-microcicle-name="<?= $microcicle->name ?>"
             class="link item microcicleOperationMenu">
            <?= $microcicle->name ?>
        </div>
    <?php endforeach; ?>
</div>

<div class="ui basic modal" id="microcicleOperation">

</div>
<?php $this->endBlock('microcicles') ?>


