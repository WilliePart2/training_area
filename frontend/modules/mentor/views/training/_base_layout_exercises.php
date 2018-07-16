<?php

/**
 * В качестве параметров принмаеться
 * 1) Список упражнений для отображения
 * 2) Флаг показывать колонку с чекбоксами или нет
*/

use common\widgets\semantic\SemanticTable;

?>
<!-- Подключаем с разметкой чекбоксов --->
<?php $this->render('_checkbox_template') ?>

<?php $table = SemanticTable::begin() ?>
    <thead>
        <tr>

            <?php if(isset($checkbox) && !empty($checkbox)): ?>
            <th class="collapsing">Выбор</th>
            <?php endif; ?>

            <?= $table->row('th', ['Упражнения']) ?>

            <th class="collapsing">1 ПМ</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($plan->trainingExercises as $trainingExercise): ?>
            <tr>

                <?php if(isset($checkbox) && !empty($checkbox)): ?>
                <td class="center aligned"
                    data-id="<?= $trainingExercise->id ?>"
                    data-name="<?= $trainingExercise->exercise->name ?>"
                    data-pm="<?= $trainingExercise->one_repeat_maximum ?>"
                ><?= $this->blocks['checkbox'] ?></td>
                <?php endif; ?>

                <?= $table->row('td', [$trainingExercise->exercise->name]) ?>

                <td class="center aligned collapsing"><?= $trainingExercise->one_repeat_maximum ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
<?php SemanticTable::end() ?>
