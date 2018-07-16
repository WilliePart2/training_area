<?php $this->beginBlock('dateParameters') ?>
<div class="ui eight wide column form microcicle_calendar">
    <div class="ui mini header">Дата начала микроцикла</div>
    <div class="three fields">
        <div class="field">
            <div class="ui selection fluid dropdown microcicle_calendarYear">
                <div class="default text">Год</div>
            </div>
        </div>
        <div class="field">
            <div class="ui selection fluid dropdown microcicle_calendarMonth">
                <div class="default text">Месяц</div>
            </div>
        </div>
        <div class="field">
            <div class="ui selection fluid dropdown microcicle_calendarDay">
                <div class="default text">День</div>
            </div>
        </div>
    </div>
</div>
<div class="column">
    <div class="ui mini header">Длительность макроцикла</div>
    <div class="ui range" id="range"></div>
    <div class="ui info message">
        Длительность <span class="microcicle_durationMessage"></span> дней.
    </div>
</div>
<?php $this->endBlock('dateParameters') ?>
