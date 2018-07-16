<?php ?>
<div class="sixteen wide column">
    <div class="ui fluid steps">
        <div class="ui <?= $step === 1 ? 'active' : 'complete' ?> step">
            <div class="content">
                <div class="title">Начальный этап</div>
                <div class="description">Заполнение идентификационной информации</div>
            </div>
        </div>
        <div class="ui <?= $step === 2 ? 'active' : '' ?> step">
            <div class="content">
                <div class="title">Заключительный этап</div>
                <div class="description">Создание базового шаблона</div>
            </div>
        </div>
    </div>
</div>
