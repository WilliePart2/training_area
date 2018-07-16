<?php ?>

<div class="ui stackable centered grid">
    <?= $this->render('_training_plan-steps' ,[
        'step' => $step
    ]) ?>
    <div class="sixteen wide column">
        <div class="error-message-place"></div>
        <div class="ui header">
            <i class="ui setting icon"></i>
            <div class="content">
                <?= $name ?>
                <div class="sub header">Редактируемый тренировочный план</div>
            </div>
        </div>
        <form class="ui fluid form init-add-exercise" method="POST">
            <div class="ui stakable centered grid">
                <div class="eight wide column">
                    <div class="ui fluid floated selection dropdown exercise-name">
                        <div class="default text">Упражнение</div>
                        <i class="ui dropdown icon"></i>
                        <div class="menu">
                            <!-- Тут будет рендериться список упражнений -->
                        </div>
                    </div>
                </div>
                <div class="four wide column">
                    <div class="ui fluid selection dropdown exercise-group">
                        <div class="default text">Целевая группа</div>
                        <i class="ui dropdown icon"></i>
                        <!-- Тут будет рендериться список целевых груп -->
                    </div>
                </div>
                <div class="four wide column">
                    <div class="ui basic primary fluid button add-exercise-btn">Добавить</div>
                </div>
            </div>
        </form>
        <table class="ui inverted celled very compact table">
            <thead>
                <tr>
                    <th>Название упражнения</th>
                    <th>1 ПМ</th>
                    <th class="collapsing">Действия</th>
                </tr>
            </thead>
            <tbody class="exercise-listing">
                <!-- Тут будет рендериться список упражнений -->
            </tbody>
        </table>
        <div class="ui basic primary button save-layout">
            Сохранить шаблон
        </div>
    </div>
</div>
