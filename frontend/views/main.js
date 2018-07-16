$('.ui.dropdown').dropdown({
    'on': 'hover'
});
$('.ui.checkbox').checkbox();
$('.with-message').popup();
jQuery.tag = function(tagName, tagOptions){
    if(tagOptions && typeof tagOptions !== 'object') throw new Error('tagOptions должен быть объектом');
    var elt = document.createElement(tagName);
    if(tagOptions){
        for(prop in tagOptions){
            if($.isArray(tagOptions[prop])){
                tagOptions[prop].forEach(function(value){
                    elt[prop] += ' ' + value;
                });
            }
            elt[prop] = tagOptions[prop];
        }
    }
    console.log(elt);
    return elt;
};

(function($){
    jQuery.fn.range = function(options){
        console.log(options);
        options = $.extend({
            'type': 'range',
            'min': 1,
            'max': 14,
            'step': 1,
            'value': 7
        }, options);
        console.log(options);
        var rangeInput = $.tag('input', options);

        return $(this).each(function(index ,elt){
            if(options.onChange){
                $(rangeInput).on('change', function(event){
                    options.onChange.call(this, this.value);
                });
            }
            if(options.output){
                var output = $(options.output).text(rangeInput.value);
                $(rangeInput).on('change', function(event){
                    output.text(this.value);
                });
            }

            return elt.append(rangeInput);
        });
    };
})(jQuery);

$('.ui.range').range({
    'output': '.microcicle_durationMessage',
    'className': 'microcicle_microcicleDuration'
});


function getAbsoluteUrl(url){
    return location.protocol + '//' + location.host + '/' + url;
}

function ExerciseAddObj(){
    this.btn = document.getElementsByClassName('add-exercise-btn')[0];
    this.sendBtn = document.getElementsByClassName('save-layout')[0];
    this.nameField = document.getElementsByClassName('exercise-name')[0];
    this.exerciseGroup = document.getElementsByClassName('exercise-group')[0];
    this.exerciseListing = document.getElementsByClassName('exercise-listing')[0];
    this.addBtn = document.getElementsByClassName('add-exercise-btn');
    this.onePmField = document.getElementsByClassName('one-pm')[0];
    this.exercises = '';
    this.groups = '';
    this.filterValue = 0;
    this.exerciseName = '';
    this.exerciseId = 0;
    this.exerciseGroupId = 0;
    this.storage = []; // Сюда будут добавляться упражнения для отправки на сервер
    this.initExerciseList = false; // Флаг иницыализации списка упражнений
    this.initGropList = false; // Флаг иницыализации списка груп
    this.initFromExerciseList = false; // Флаг указывает что иницыализация идет от списка упражнений

    if(this.btn) {
        $(this.btn).on('click', this.addHandler);
    }
};

ExerciseAddObj.prototype.createExerciseList = function(){
    var self = this;
    if(self.nameField && self.exercises){
        var valuesToDropdown = [];

        this.exercises.forEach(function(item){
            if(self.filterValue && self.filterValue !== item.group_id) return;
            valuesToDropdown.push({
                'name': item.name,
                'value': item.id + '_' + item.group_id
            });
        });

        $(this.nameField).dropdown({
            values: valuesToDropdown,
            onChange: function(ids, exerciseName){
                /** Иницыализируем список упражннеий */
                if(!self.initExerciseList) {
                    self.initExerciseList = true;
                    return
                }

                self.exerciseName = exerciseName;
                self.exerciseGroupId =ids.substr(ids.indexOf('_') + 1);
                self.exerciseId = ids.substr(0 ,ids.indexOf('_'));

                /** Останавлиаем фильтр для груп относительно выбраного упражнения */
                if(!self.filterValue) {
                    self.initFromExerciseList = true;
                    self.createGroupList();
                }
            }
        });
    }
};
ExerciseAddObj.prototype.createGroupList = function(){
    var self = this;
    if(this.exerciseGroup && this.groups){
        var valuesToDropdown = [];
        this.groups.forEach(function(item){
            if(self.exerciseGroupId && !self.filterValue) {
                if(parseInt(item.id) === parseInt(self.exerciseGroupId)){
                    valuesToDropdown.push({
                        'name': item.muskul_group,
                        'value': item.id,
                        'selected': true
                    });

                    return;
                }
            }
            valuesToDropdown.push({
                'name': item.muskul_group,
                'value': item.id
            });
        });
        $(this.exerciseGroup).dropdown({
            values: valuesToDropdown,
            onChange: function(value){
                /** Иницыализируем список групп - блокировка срабатывания обработчика при первоначальном добавлении данных */
                if(!self.initGroupList){
                    self.initGroupList = true;
                    return;
                }

                /** Блокирует пересборку списка упражнений */
                if(self.initFromExerciseList === false) {
                    self.filterValue = parseInt(value);
                    self.createExerciseList();
                }
            }
        });

        /** Обработчик события который снимает блокировку фильтрации по группе */
        $(this.exerciseGroup).on('click', function(){
            self.initFromExerciseList = false;
        });
    }
};
ExerciseAddObj.prototype.addBtnHandler = function(callback){
    if(this.addBtn){
        var self = this;
        $(this.addBtn).on('click', function(){
            var elt = $(self.exerciseListing);
            var oldHtml = elt.html();
            elt.html(oldHtml + '<tr><td data-id="' + self.exerciseId + '">' + self.exerciseName + '</td><td><div class="ui fluid transparent input"><input class="one-pm" type="text" placeholder="Введите 1ПМ" /></div></td><td><div class="ui basic inverted button">Удалить</div></td></tr>');

            self.storage.push({
                'exerciseId': self.exerciseId
            });

            self.onePmField = document.getElementsByClassName('one-pm');
            Array.prototype.forEach.call(self.onePmField ,function(field){
                $(field).on('change', self.updateOnePm.bind(self));
            });
        });
    }

    if(this.sendBtn){
        $(this.sendBtn).on('click', this.sendData.bind(this));
    }
};
ExerciseAddObj.prototype.updateOnePm = function(event){
    var target = $(event.target);
    var value = parseInt(target.val());
    var exerciseId = target.closest('td').prev().attr('data-id');
    this.storage = this.storage.map(function(item){
        if(item.exerciseId === exerciseId) {
            item.onePM = value;
            return item;
        }

        return item;
    });
};
ExerciseAddObj.prototype.loadData = function(arrayOfCallbacks){
    var self = this;
    $.ajax({
        url: getAbsoluteUrl('mentor/training/get-exercises'),
        type: 'GET',
        complete: function(arguments){
            self.exercises = JSON.parse(arguments.responseText).exercises;
            self.groups = arguments.responseJSON.groups;

            if(arrayOfCallbacks){
                arrayOfCallbacks.forEach(function(callback){
                    callback.apply(self);
                });
            }
        }
    });
};
ExerciseAddObj.prototype.sendData = function(){
    console.log('+');
    var validate = this.validateData();

    if(validate) {
        var self = this;
        $.ajax({
            'url': Location.href,
            'type': 'POST',
            'data': {
                'exercises': JSON.stringify(this.storage)
            },
            'complete': function (arguments) {
                // пока что стоит заглушка потому что скрипт yii2 сам перенаправляет меня на нужную страницу
            }
        });
    }
};
ExerciseAddObj.prototype.validateData = function(){
    var hasError = false;
    if(!this.onePmField) return false;
    Array.prototype.forEach.call(this.onePmField, function(field){
        if(hasError) return;

        field = $(field);
        if(!field.val()) {
            $('.error-message-place').html('<div class="ui error message">Все поля 1 ПМ должны быть заполнены</div>');
            hasError = true;
        }
    });
    return !hasError;
};


var initForm = document.getElementsByClassName('init-add-exercise')[0];
if(initForm){
    var controller = new ExerciseAddObj();
    controller.loadData([
        controller.createGroupList,
        controller.createExerciseList,
        controller.addBtnHandler
    ]);
}

function MicrocicleTrainingAdd(){
    this.trainingNameField = document.getElementsByClassName('microcicle_trainingNameField')[0];
    // this.trainingReadmeField = document.getElementsByClassName('microcicle_trainingReadme')[0];
    this.trainingAddBtn = document.getElementsByClassName('microcicle_trainingAddBtn')[0];
    this.trainingRender = document.getElementsByClassName('microcicle_trainingRender')[0];
    this.selectCheckboxes = document.getElementsByClassName('target-checkbox');
    this.container = document.getElementsByClassName('microcicle_init')[0];
    this.messageBox = document.getElementsByClassName('microcicle_messageBox')[0];

    this.storageExercises = []; // Это хранилище упражнений для конкретной тренировки
        // Тренировки будут храниться в виде объектов
    this.hasRenderedTrainings = []; // Сюда ьудут собираться все элементы которые уже отрендерились

    /** В этом объеке будут храниться временные данные по раскладкам для упражнений */
    this.exerciseDataStorage = [];

    /** Счетчик для раскадок внутри упражнения */
    this.planCounter = 0;

    /** Счетчик для упражнений */
    this.counter = 1;
}
MicrocicleTrainingAdd.prototype.errorMessage = function(message){
    if(this.container && this.messageBox){
        $(this.messageBox).html('<div class="ui error message">' + message + '</div>');
    }
};
MicrocicleTrainingAdd.prototype.deleteErrorMessage = function(){
    $(this.messageBox).html('');
};
MicrocicleTrainingAdd.prototype.validateData = function(){
    if(!this.trainingNameField || !$(this.trainingNameField).val()) return false;
    if(this.storageExercises.length == 0) return false;
    return true;
};
MicrocicleTrainingAdd.prototype.createElt = function(elt, text, attr){
    var element = $(document.createElement(elt));
    if(text) element.text(text);

    if(attr && typeof attr === 'object'){
        for(attrName in attr){
            /** если элемент\элементы переданы с ключем children вставляем их как дочерние */
            if(attrName === 'children'){
                if(attr[attrName] && $.isArray(attr[attrName]) && attr[attrName].length){
                    attr[attrName].forEach(function(elt){
                        element.append(elt);
                    });
                    continue;
                }

                element.append(attr[attrName]);
                continue;
            }

            element.attr(attrName, attr[attrName]);
        }
    }

    return element;
};

/**
 * Метод генерирует данные необхадимые для сохранения тренировки в хранилище
 * @exercise - данные о упражнении из временного хранилища(для чекбоксов)
 * @trainingName - уже измененное имя тренировки
 */
MicrocicleTrainingAdd.prototype.prepareDataToSave = function(exercise, trainingName){
    var data = {
        'name': exercise.name,
        'id': this.counter + '_' + exercise.id + '_' + exercise.name + '_' + trainingName + '_' + Math.random() + Date.now(),
        'pm': exercise.pm
    }
    return data;
};

/** Метод обрабатывает добавление тренировки */
MicrocicleTrainingAdd.prototype.addHandler = function(){
    var validate = this.validateData();
    if(!validate) {
        this.errorMessage('Нужные поля не заполнены');
        return false;
    }

    var self = this;
    var trainingName = $(this.trainingNameField).val().trim();
    var count = 1;
    var header = this.createElt('div', null, {
        'class': 'ui fluid left floated header',
        'children': [
            this.createElt('span', trainingName, {'class': 'ui middle aligned'})
        ]
    });
    trainingName += '_' + Math.random() + '_' + Date.now();
    var trainingDeleteBtn = this.createElt('div', 'Удалить', {
        'class': 'ui mini basic secondary right floated button microcicle_deleteTrainingBtn'
    });
    var container = $(this.createElt('div', null, {
        'class': 'ui basic segment',
        'id': trainingName,
        'children': [
            header,
            trainingDeleteBtn,
            this.createElt('div', null, {'class': 'ui fitted clearing divider'})
        ]
    }));

    // container.append(header);
    // container.append(trainingDeleteBtn);

    var table = $(document.createElement('table'));
    table.addClass('ui single row very compact inverted selectable celled table');
    var thead = this.createElt('thead');
    var tbody = this.createElt('tbody');
    var tr = this.createElt('tr', null, {
        'children': [
            this.createElt('th', '№', {'class': 'collapsing'}),
            this.createElt('th', 'Упражнение'),
            this.createElt('th', '1 ПМ', {'class': 'collapsing'}),
            this.createElt('th','Вес', {'class': 'collapsing'}),
            this.createElt('th','Повторения', {'class': 'collapsing'}),
            this.createElt('th', 'Подходы', {'class': 'collapsing'}),
            this.createElt('th', 'КПШ', {'class': 'collapsing'}),
            this.createElt('th', 'Тоннаж', {'class': 'collapsing'}),
            this.createElt('th', 'Ср. вес', {'class': 'collapsing'}),
            this.createElt('th', 'Отн. инт', {'class': 'collapsing'}),
            this.createElt('th', 'Действия', {
                'class': 'collapsing center aligned'
            })
        ]
    });
    thead.append(tr);
    table.append(thead);
    Array.prototype.forEach.call(this.storageExercises ,function(exercise){
        var preparedData = self.prepareDataToSave(exercise, trainingName);
        var tr = self.createElt('tr');
        var prefixId = Math.random() + '' + Date.now();
        var id = preparedData.id;
        tr.attr('id', id);

        tr.append(self.createElt('td', self.counter, {'class': 'collapsing top aligned center aligned'}));
        tr.append(self.createElt('td', exercise.name, {'class': 'top aligned'}));
        tr.append(self.createElt('td', exercise.pm, {'class': 'collapsing center aligned top aligned'}));
        tr.append(self.createElt('td', '', {
            'children': self.createElt('div', '', {
                'class': 'ui fluid transparent input',
                'children': self.createElt('input', null, {
                    'class': 'training_weight',
                    'type': 'text'
                })
            })}));
        tr.append(self.createElt('td', null, {
            'children':self.createElt('div', null, {
                'class': 'ui fluid transparent input',
                'children': self.createElt('input', null, {
                    'class': 'training_repeat',
                    'type': 'text'
                })
            })
        }));
        tr.append(self.createElt('td', null, {
            'children':self.createElt('div', null, {
                'class': 'ui fluid transparent input',
                'children': self.createElt('input', null, {
                    'class': 'training_repeatSection',
                    'type': 'text'
                })
            })
        }));
        tr.append(self.createElt('td'));
        tr.append(self.createElt('td'));
        tr.append(self.createElt('td'));
        tr.append(self.createElt('td'));
        tr.append(self.createElt('td', '', {
            'class': 'center aligned',
            'children': [
                self.createElt('div', '', {
                'class': 'ui basic inverted mini compact icon button training_addSubPlan',
                'children': self.createElt('i','',{'class': 'ui plus icon'})
                }),
                self.createElt('div', null, {
                    'class': 'ui basic compact mini icon inverted button training_exerciseDeleteBtn',
                    'children': self.createElt('i', null, {'class': 'ui delete icon'})
                })
            ]}));
        tbody.append(tr);
        tbody.append(tr);

        self.hasRenderedTrainings.push({
            'trainingName': trainingName,
            'exerciseName': exercise.name,
            'exerciseId': id,
            'exercisePM': exercise.pm,
            'trainingPlans': []
        });

        self.counter++;
    });
    table.append(tbody);

    var tr = this.createElt('tr', null, {'id': 'totalMonitor' + trainingName});
    tr.append(this.createElt('th', null));
    tr.append(this.createElt('th', 'Общие данные'));
    tr.append(this.createElt('th', null));
    tr.append(this.createElt('th', null));
    tr.append(this.createElt('th', null));
    tr.append(this.createElt('th', null));
    tr.append(this.createElt('th', "0", {'class': 'center aligned'})); // КПШ
    tr.append(this.createElt('th', "0", {'class': 'center aligned'})); // Тоннаж
    tr.append(this.createElt('th', "0", {'class': 'center aligned'})); // Средний вес
    tr.append(this.createElt('th', "0", {'class': 'center aligned'})); // Относительная интенсивность
    tr.append(this.createElt('th', null));

    var tfoot = this.createElt('tfoot', null, {
        'children': tr
    });


    table.append(tfoot);

    container.append(table);
    container.append('<div class="ui hidden divider"></div>');
    $(this.trainingRender).append(container);

    /** Сбрасываем поля ввода начальных данных и переиницыализируем обработчики */
    this.resetStartData();
    this.trainingManipulationInit();

};
/** Метод сбрасывает выбраные чекбоксы и поле имени тренировки */
MicrocicleTrainingAdd.prototype.resetStartData = function(){
    this.storageExercises = [];

    Array.prototype.forEach.call(this.selectCheckboxes, function(item){
        $(item).checkbox('set unchecked') && $(item).closest('tr').removeClass('active');
    });
    $(this.trainingNameField).val('');
    this.deleteErrorMessage();
};

/**
 * Этот метод обрабатвает указание данных раскладки
 */
MicrocicleTrainingAdd.prototype.trainingManipulationInit = function(){
    this.addPlanBtn = document.getElementsByClassName('training_addSubPlan');
    this.weightField = document.getElementsByClassName('training_weight');
    this.trainingRepeatField = document.getElementsByClassName('training_repeat');
    this.repeatSectionField = document.getElementsByClassName('training_repeatSection');
    this.deletePlanBtn = document.getElementsByClassName('training_deletePlanBtn');
    this.deleteExerciseBtn = document.getElementsByClassName('training_exerciseDeleteBtn');
    this.deleteTrainingBtn = document.getElementsByClassName('microcicle_deleteTrainingBtn');
    this.editTrainingBtn = document.getElementsByClassName('microcicle_trainingEditBtn');

    var self = this;
    Array.prototype.forEach.call(this.addPlanBtn, function(btn){
        $(btn).on('click', function(event){
            self.addPlanHandler.call(self, event);
        });
    });
    Array.prototype.forEach.call(this.weightField, function(item){
        $(item).on('change', function(event) {
                self.changePlanPropertyHandler.call(self, event, 'weight');
            });
    });
    Array.prototype.forEach.call(this.trainingRepeatField, function(item){
        $(item).on('change', function(event){
            self.changePlanPropertyHandler.call(self, event, 'repeat');
        });
    });
    Array.prototype.forEach.call(this.repeatSectionField, function(item){
        $(item).on('change', function(event){
            self.changePlanPropertyHandler.call(self, event, 'repeatSections');
        });
    });

    if(this.deletePlanBtn[0]){
        Array.prototype.forEach.call(this.deletePlanBtn, function(item){
            $(item).on('click', function(event){
                self.deletePlanHandler.call(self, event);
            });
        });
    }
    if(this.deleteExerciseBtn[0]){
        Array.prototype.forEach.call(this.deleteExerciseBtn, function(item){
            $(item).on('click', function(event){
                self.deleteExerciseHandler.call(self, event);
            });
        });
    }
    if(this.deleteTrainingBtn[0]){
        Array.prototype.forEach.call(this.deleteTrainingBtn,function(item){
            $(item).on('click', function(event){
                self.deleteTrainingHandler.call(self, event);
            });
        });
    }
    /**
     * Иницыализация обработчика добавления упражнений в существующую тренировку
     */
    if(this.editTrainingBtn[0]){
        Array.prototype.forEach.call(this.editTrainingBtn, function(item){
            var init = false
            $(item).dropdown({
                'on': 'click',
                'values': self.getRegisteredTrainings(),
                'onChange': function(value){
                    if(!init) {
                        init = true;
                        return;
                    }
                    self.addExerciseInTraining(value);
                    self.reRenderExerciseTable(value);
                }
            });
        });
    }
};
/** Метод обрабатывает изменения в полях "вес"б "повторения"б "подходы" и вызывает перерендер */
MicrocicleTrainingAdd.prototype.changePlanPropertyHandler = function(event ,property){
    console.log('event in field handler');
    console.log(event);
    var target = $(event.target);
    console.log('target in field handler');
    console.log(target);
    var row = target.closest('tr');
    console.log('row in field handler');
    console.log(row);
    var rowId = row.attr('id');
    var value = target.val();
    var dataItem = this.findDataItem(rowId);
    if(!dataItem){
        dataItem = this.createEmptyDataItem(rowId);
    };
    console.log('Id from field handler: ' + rowId);
    var newDataItem = this.setPropertyData(dataItem, property, value);
    this.saveDataItem(newDataItem);
    // row.html('');
    // this.reRenderMainPartOfRow(rowId);
    // this.renderPlanToRow(rowId);
    // this.trainingManipulationInit();
    this.fullReRender(rowId, false);
};
/** Метод копирует объект, изменяет в нем нужное свойсвто и возвращает копированый объект */
MicrocicleTrainingAdd.prototype.setPropertyData = function(object, property, data){
    if(typeof object !== 'object') return false;

    var newObj = {};
    for(prop in object){
        if(property && (prop.toLowerCase() === property.toLowerCase())) {
            newObj[prop] = data;
            continue;
        }
        newObj[prop] = object[prop];
    }
    return newObj;
};
/** Этот метод ищет объект по идентификатору в exerciseDataStorage */
MicrocicleTrainingAdd.prototype.findDataItem = function(id){
    if(!this.exerciseDataStorage.length) return false;

    var result = null;
    this.exerciseDataStorage.forEach(function(item){
        if(result) return;
        if(item.id === id) result = item;
    });

    return result;
};
/** Этот метод ищет объект по идентификатору в уже отрендереных упражнениях */
MicrocicleTrainingAdd.prototype.findRenderedExercise = function(id){
    if(!this.hasRenderedTrainings.length) return false;

    var result = null;
    this.hasRenderedTrainings.forEach(function(item){
        if(result) return;
        if(item.exerciseId === id) result = item;
    });

    return result;
};
/** Этот метод сохраняет объект в exerciseDataStorage при этом удаляя старый объект */
MicrocicleTrainingAdd.prototype.saveDataItem = function(dataItem){
    if(!this.exerciseDataStorage.length) {
        this.exerciseDataStorage.push(dataItem);
    } else {
        var self = this;
        var oldStorage = this.exerciseDataStorage;
        this.exerciseDataStorage = [];
        oldStorage.forEach(function (item) {
            if (item.id === dataItem.id)  return;
            else self.exerciseDataStorage.push(item);
        });
        this.exerciseDataStorage.push(dataItem);
    }
};
/** Этот методы ищет нужные данные в exerciseDataStorage b hasRenderedStorage и перерендеривает содержимое нужной строки */
MicrocicleTrainingAdd.prototype.reRenderMainPartOfRow = function(id){
    console.log('id in render main part of row' + id);
    var row = $(document.getElementById(id));
    var dataItem = this.findDataItem(id);
    var renderedItem = this.findRenderedExercise(id);
    console.log('data from rerender main part of row:');
    console.log('src data:');
    console.log(this.exerciseDataStorage);
    console.log(this.hasRenderedTrainings);
    console.log('result data:');
    console.log(dataItem);
    console.log(renderedItem);
    if(!renderedItem) return false; // было !dataItem || !renderedItem
    if(!dataItem) dataItem = this.createEmptyDataItem(id);
    row.html("");

    var number = renderedItem.exerciseId.substr(0, renderedItem.exerciseId.indexOf('_'));
    var weight = parseInt(dataItem.weight);
    var repeat = parseInt(dataItem.repeat);
    var repeatSections = parseInt(dataItem.repeatSections);

    if(repeat && !repeatSections) repeatSections = 1;
    if(!repeat && repeatSections) repeat = 1;

    var PM = parseInt(renderedItem.exercisePM);
    var KPSH = (repeat * repeatSections);

    var tr = row;
    tr.append(this.createElt('td', number, {'class': 'top aligned center aligned'}));
    tr.append(this.createElt('td', renderedItem.exerciseName, {'class': 'top aligned'}));
    tr.append(this.createElt('td', renderedItem.exercisePM, {'class': 'top aligned center aligned'}));
    tr.append(this.createElt('td', null, {
        'children': [
            this.createElt('div', null, {'class': 'ui hidden fitted divider'}),
            this.createElt('div', null, {
                'class': 'ui fluid transparent input top aligned',
                'children': this.createElt('input', null, {
                    'type': 'text',
                    'class': 'ui top aligned training_weight',
                    'value': dataItem.weight || 0
                })
            }),
            this.createElt('div', null, {'class': 'ui hidden fitted divider'})
        ]
    }));
    tr.append(this.createElt('td', null, {
        'children': [
            this.createElt('div', null, {'class': 'ui hidden fitted divider'}),
            this.createElt('div', null, {
                'class': 'ui fluid transparent input top aligned',
                'children': this.createElt('input', null, {
                    'value': dataItem.repeat || 0,
                    'class': 'ui top aligned training_repeat',
                    'type': 'text'
                })
            }),
            this.createElt('div', null, {'class': 'ui hidden fitted divider'})
        ]
    }));
    tr.append(this.createElt('td', null, {
        'children': [
            this.createElt('div', null, {'class': 'ui hidden fitted divider'}),
            this.createElt('div', null, {
                'class': 'ui fluid transparent input top aligned',
                'children': this.createElt('input', null, {
                    'type': 'text',
                    'class': 'ui top aligned training_repeatSection',
                    'value': dataItem.repeatSections || 0
                })
            }),
            this.createElt('div', null, {'class': 'ui hidden fitted divider'})
        ]
    }));
    tr.append(this.createElt('td', null, {
        'class': 'center aligned top aligned',
        'children': [
            this.createElt('div', null, {'class': 'ui hidden fitted divider'}),
            this.createElt('div', KPSH || '0', {'class': 'training_monitor'}),
            this.createElt('div', null, {'class': 'ui hidden fitted divider'})
        ]
    }));
    tr.append(this.createElt('td', null, {
        'class': 'center aligned  top aligned',
        'children': [
            this.createElt('div', null, {'class': 'ui hidden fitted divider'}),
            this.createElt('div', (KPSH * weight) || "0", {'class': 'training_monitor'}),
            this.createElt('div', null, {'class': 'ui hidden fitted divider'})
        ]
    }));
    tr.append(this.createElt('td', null, {
        'class': 'center aligned top aligned',
        'children': [
            this.createElt('div', null, {'class': 'ui hidden fitted divider'}),
            this.createElt('div', ((KPSH * weight) / KPSH) || "0", {'class': 'training_monitor'}),
            this.createElt('div', null, {'class': 'ui hidden fitted divider'})
        ]
    }));
    tr.append(this.createElt('td', null, {
        'class': 'center aligned top aligned',
        'children': [
            this.createElt('div', null, {'class': 'ui hidden fitted divider'}),
            this.createElt('div', ((Math.round(((weight / PM) * 100))) || "0") + '%', {'class': 'training_monitor'}),
            this.createElt('div', null, {'class': 'ui hidden fitted divider'})
        ]
    }));
    tr.append(this.createElt('td', null, {
        'class': 'center aligned top aligned',
        'children': [
            this.createElt('div', null, {
            'class': 'ui basic compact mini icon inverted button training_addSubPlan',
            'children': this.createElt('i', null, {'class': 'ui plus icon'})
        }),
            this.createElt('div', null, {
                'class': 'ui basic compact mini icon inverted button training_exerciseDeleteBtn',
                'children': this.createElt('i', null, {'class': 'ui delete icon'})
            })
        ]
    }));
};
/**
 * Метод создает пустой объект хранения для временного хранилища данных exerciseDataStorage
 */
MicrocicleTrainingAdd.prototype.createEmptyDataItem = function(id){
    return {
        'id': id,
        'weight': 0,
        'repeat': 0,
        'repeatSections': 0
    };
};

/**
 * Метод обрабатывает добавление раскладки в тренировку(в таблицу + в объект постоянного хранения hasRenderedTrainings)
 */
MicrocicleTrainingAdd.prototype.addPlanHandler = function(event){
    var target = $(event.target);
    var row = target.closest('tr');
    var rowId = row.attr('id');
    console.log('id in add plan handler: ' + rowId);

    var dataItem = this.findDataItem(rowId);

    if(!dataItem) return;
    /** Предотвращает ввод нечисловых данных */
    if(!this.validateDataItem(dataItem)) {
        this.errorMessage('В поля раскладки можно вводить только цифры');
        return;
    }
    /** Предотвращает ввод пустых данных */
    for(prop in dataItem){
        if(!dataItem[prop]) {
            this.errorMessage('Все поля раскладки должны быть заполнены');
            return;
        }
    }

    var newDataItem = this.setPropertyData(dataItem, 'id', (this.planCounter++) + rowId); // Получаем новый объект
    var renderItem = this.findRenderedExercise(rowId);
    newDataItem.exercisePM = renderItem.exercisePM;
    this.savePlanInStorage(newDataItem, rowId); // Тут передаеться объект по ссылке. УСТРАНИТЬ ЭТО! Устранил =)
    // this.renderPlanToRow(rowId);
    this.fullReRender(rowId, true, this.planCounter + rowId);
    this.clearFields(rowId);
    this.renterTotalDataOfExercise(renderItem.trainingName);

    this.deleteErrorMessage();
};
/** Метод проводит валидацию данных раскладки */
MicrocicleTrainingAdd.prototype.validateDataItem = function(dataItem){
    var result = true;
    for(prop in dataItem){
        if(prop === 'id') continue;
        console.log(dataItem[prop]);
        if(dataItem[prop] && dataItem[prop].toString().search(/^\d+$/) === -1) {
            result = false;
        }
    }
    return result;
};
/** Метод полностью перерендеривает строку */
MicrocicleTrainingAdd.prototype.fullReRender = function(rowId, clearRow/* флаг - очищать строку и временное хранилище или нет */){
    console.log('id in full rerender: ' + rowId);
    var row = $(document.getElementById(rowId));
    // row.html('');
    this.reRenderMainPartOfRow(rowId);
    this.renderPlanToRow(rowId);
    if(clearRow) {
        this.clearFields(rowId);
        this.exerciseDataStorage = []; // Обнуляем временное хранилище
        this.renderTotalString(rowId);
    }
    this.trainingManipulationInit();
};
/** Метод добавляет объект раскладки в постоянное хранилище */
MicrocicleTrainingAdd.prototype.savePlanInStorage = function(planObj, id){
    if(typeof planObj !== 'object') return;
    // planObj.id = (this.planCounter++) + id; // Создаем id для раскладки
    var plansForExercise = null;

    // Находим тренировочные планы нужного упражнения
    this.hasRenderedTrainings.forEach(function(item){
        if(item.exerciseId === id) plansForExercise = item.trainingPlans; // Тут был баг! Вкладывал один пустой масив в другой
    });

    console.log(planObj);
    // Сохраняем наш объект или заменяем на новый, если такой уже есть
    var tmp = [];
    console.log(plansForExercise);
    console.log(tmp);
    plansForExercise.forEach(function(item){
        if(item && item.id === planObj.id) return;
        tmp.push(item);
    });
    tmp.push(planObj);
    plansForExercise = tmp;
    console.log(plansForExercise);

    // Заменяем тренировочные планы, в нужном упражнении, на новый масив
    var self =this;
    var temp = this.hasRenderedTrainings;
    this.hasRenderedTrainings = [];
    temp.forEach(function(item){
        if(item.exerciseId === id) {
            item.trainingPlans = plansForExercise;
            self.hasRenderedTrainings.push(item);
            return
        }
        self.hasRenderedTrainings.push(item);
    });

};
/** Метод устанавливает поля ввода даных раскладки в 0 */
MicrocicleTrainingAdd.prototype.clearFields = function(rowId){
    var row = $(document.getElementById(rowId));
    var weightField = row.find('.training_weight').val(0);
    var repeatField = row.find('.training_repeat').val(0);
    var repeatSection = row.find('.training_repeatSection').val(0);
    var kpshField = repeatSection.closest('td').next();
    kpshField.find('.training_monitor').text(0);
    var tonnageField = kpshField.next();
    tonnageField.find('.training_monitor').text(0);
    var averageWeightField = tonnageField.next();
    averageWeightField.find('.training_monitor').text(0);
    var intFIeld = averageWeightField.next();
    intFIeld.find('.training_monitor').text(0);
};
/** Метод рендерит раскадку в строку с полем */
MicrocicleTrainingAdd.prototype.renderPlanToRow = function(rowId){
    var self = this;
    if(typeof rowId === 'object'){
        var trainingExercise = this.findRenderedExercise(rowId.attr('id'));
    } else {
        var row = $(document.getElementById(rowId));
        var trainingExercise = this.findRenderedExercise(rowId);
    }
    console.log('id in render plan to row:' + rowId);
    console.log(this.hasRenderedTrainings);
    console.log(trainingExercise);

    if(!trainingExercise) return;

    var plans = trainingExercise.trainingPlans;
    console.log('Plans');
    console.log(plans);
    if(!plans) return;
    // var weightField = row.find('.training_weight').closest('td');
    // var repeatField = row.find('.training_repeat').closest('td');
    // var repeatSectionField = row.find('.training_repeatSection').closest('td');
    // var kpshField = repeatSectionField.next();
    // var tonnageField = kpshField.next();
    // var averageWeightField = tonnageField.next();
    // var intField = averageWeightField.next();
    // var btnField = intField.next();

    plans.forEach(function(item){
        if(!item || (item && !item.id)) return;
        var weight = parseInt(item.weight);
        var repeat = parseInt(item.repeat);
        var repeatSection = parseInt(item.repeatSections);
        var PM = parseInt(item.exercisePM);

        if(repeat && !repeatSection) repeatSection = 1;
        if(!repeat && repeatSection) repeat = 1;

        var KPSH = repeat * repeatSection;

        self.generateTableRow([
            null,
            null,
            null,
            [
                self.createElt('div', null, {'class': 'ui hidden fitted divider'}),
                self.createElt('div', weight)
            ],
            [
                self.createElt('div', null, {'class': 'ui hidden fitted divider'}),
                self.createElt('div', repeat)
            ],
            [
                self.createElt('div', null, {'class': 'ui hidden fitted divider'}),
                self.createElt('div', repeatSection)
            ],
            [
                self.createElt('div', null, {'class': 'ui hidden fitted divider'}),
                self.createElt('div', KPSH)
            ],
            [
                self.createElt('div', null, {'class': 'ui hidden fitted divider'}),
                self.createElt('div', KPSH * weight)
            ],
            [
                self.createElt('div', null, {'class': 'ui hidden fitted divider'}),
                self.createElt('div', (KPSH * weight) / KPSH)
            ],
            [
                self.createElt('div', null, {'class': 'ui hidden fitted divider'}),
                self.createElt('div', (Math.round((weight/PM) * 100) || 0) + '%')
            ],
            [
                self.createElt('div', null, {'class': 'ui fitted hidden divider'}),
                self.createElt('i', null, {
                    'class': 'ui link delete icon training_deletePlanBtn',
                    'id': item.id
                })
            ]
        ], rowId, null, null, true);
        // weightField.append(self.createElt('div', weight));
        // repeatField.append(self.createElt('div', repeat));
        // repeatSectionField.append(self.createElt('div', repeatSection));
        // kpshField.append(self.createElt('div', KPSH));
        // tonnageField.append(self.createElt('div', KPSH * weight));
        // averageWeightField.append(self.createElt('div', (KPSH * weight) / KPSH));
        // intField.append(self.createElt('div', Math.round((weight / PM) * 100) + '%'));
        // btnField.append(self.createElt('div', null, {'class': 'ui fitted hidden divider'}));
        // btnField.append(self.createElt('i', null, {
        //     'class': 'ui link delete icon training_deletePlanBtn',
        //     'id': item.id // Это идентификатор раскладки в упражнении
        // }));
    });
};
/** Метод собирает данные по раскладкам и возвращает их в виде объекта */
MicrocicleTrainingAdd.prototype.getFullData = function(rowId){
    var total = {
        totalKpsh: 0,
        totalTonnage: 0,
        totalAverageWeight: 0,
        totalAverageInt: 0
    };
    var renderedItem = this.findRenderedExercise(rowId);
    if(!renderedItem || !renderedItem.trainingPlans.length) return total;
    var counter = 0;
    renderedItem.trainingPlans.forEach(function(item){
        if(!item) return;

        var weight = parseInt(item.weight);
        var repeat = parseInt(item.repeat);
        var repeatSections = parseInt(item.repeatSections);
        if(!repeat && repeatSections) repeat = 1;
        if(repeat && !repeatSections) repeat = 1;
        var kpsh = repeat * repeatSections;

        total.totalKpsh += kpsh;
        total.totalTonnage += kpsh * weight;
    });
    total.totalAverageWeight = Math.round(total.totalTonnage / total.totalKpsh);
    total.totalAverageInt = Math.round(((total.totalAverageWeight / parseInt(renderedItem.exercisePM)) * 100));
    return total;
};
/** Метод рендерит строку в футере таблицы с общими характеристиками тренировки */
MicrocicleTrainingAdd.prototype.renterTotalDataOfExercise = function(trainingName){
    var self = this;
    var total = {
        'kpsh': 0,
        'tonnage': 0,
        'averageWeight': 0,
        'averageInt': 0
    };

    // Получаем список упражнений в тренировке
    var exercises = [];
    this.hasRenderedTrainings.forEach(function(item){
        if(item.trainingName === trainingName && item.trainingPlans.length) exercises.push(self.setPropertyData(item));
    });
    console.log(exercises);
    var countExercises = exercises.length;
    exercises.forEach(function(item){
        if(!item.exerciseId) return;
        console.log(item);

        var tmpTotal = self.getFullData(item.exerciseId);
        console.log(tmpTotal);

        total.kpsh += tmpTotal.totalKpsh;
        total.tonnage += tmpTotal.totalTonnage;
        console.log(tmpTotal.totalAverageInt);
        console.log(total.averageInt);

        console.log(parseInt(total.averageInt) + parseInt(tmpTotal.totalAverageInt));

        total.averageInt = parseInt(total.averageInt) + parseInt(tmpTotal.totalAverageInt);
    });
    total.averageWeight = Math.round(total.tonnage / total.kpsh);
    total.averageInt = Math.round(total.averageInt / countExercises) || 0;

    var container = $(document.getElementById('totalMonitor' + trainingName));
    container.empty();
    container.append(this.generateTableRow([
        null,
        this.createElt('th', 'Общие данные', {'class': 'left aligned'}),
        null,
        null,
        null,
        null,
        total.kpsh,
        total.tonnage,
        total.averageWeight,
        total.averageInt + '%',
        null
    ], 'totalMonitor' + trainingName, 'th'));
};

/** Метод рендерит строку с общими характеристиками */
MicrocicleTrainingAdd.prototype.renderTotalString = function(rowId){
    console.log('render total string is init');
    console.log('id in render total row: ' + rowId);
    var totalData = this.getFullData(rowId);
    console.log(totalData);
    var data = [
        null,
        null,
        null,
        null,
        null,
        null,
        totalData.totalKpsh,
        totalData.totalTonnage,
        totalData.totalAverageWeight,
        totalData.totalAverageInt + '%',
        null
    ];

    var mainRow = $(document.getElementById(rowId));
    console.log(mainRow);
    rowId = 'total_' + rowId;
    var totalRow = document.getElementById(rowId);

    if(!totalRow){
        var row = this.generateTableRow(data);
        row.attr('id', rowId);
        console.log(row);
        mainRow.after(row);
        return;
    }
    totalRow = $(totalRow);
    totalRow.html('');
    this.generateTableRow(data, rowId);
};
/**
 * Метод генерирует строку таблицы
 * @args - содержимое ячеет таблицы
 * @rowId - идентификатор строки в которую добавить содержимое
 * @element - HTML элемент который создавать
 * @params - параметры для элементов
 * Метод возвращает либо новосозданую строку таблицы либо ссылку на объект DOM
 */
MicrocicleTrainingAdd.prototype.generateTableRow = function(args, rowId, element, params, exists){
    if(element && typeof element !== 'string') throw new Error('element должен быть строкой');

    var self = this;
    var elt = element ? element : 'td';
    var params = params ? params : {'class': 'center aligned'};
    if(rowId !== null && typeof rowId === 'object') {
        var tr = rowId;
    } else {
        var tr = !rowId ? $(this.createElt('tr')) : $(document.getElementById(rowId));
    }
    if(!args) return tr;

    function appendDomElt(container, elt){
        return container.append(elt);
    }

    /** Вставка в уже существующие колонки строки */
    var currentElt = true;
    if(exists && currentElt){
        currentElt = tr.find('td').first(); // Поставить проверочку
        args.forEach(function(item){
            if(!currentElt) return;
            if(!item) {
                currentElt = currentElt.next();
                return;
            }
            console.log(currentElt);

            /** Обрабатываем случай если передан элемент DOM */
            if(item && typeof item === 'object' && !$.isArray(item)){
                currentElt.append(item);
                currentElt = currentElt.next();
                return;
            }

            /** Обрабатываем случай когда item являеться масивом чего то */
            if($.isArray(item)){
                item.forEach(function(secondLevelItem){
                    /** secondLevelItem являеться масивом DOM элементов */
                    if(secondLevelItem && typeof secondLevelItem === 'object'){
                        currentElt.append(secondLevelItem);
                        return;
                    }

                    /** item являеться масивом с простым конентом элемента (оставлю закоментированным пока не решу что делать)*/
                    /* currentElt.append(self.createElt(elt, secondLevelItem, params)); */
                });
                currentElt = currentElt.next();
                return;
            }

            currentElt.append(self.createElt(elt, item, params));
            currentElt = currentElt.next();
        });

        return;
    }

    /** Вставка в пустую строку */
    args.forEach(function(item){
        /** Если item являеться DOM элементом */
        if(item && typeof item === 'object' && !$.isArray(item)){
            tr.append(item);
            return;
        }

        /** Если item являеться масивом */
        if($.isArray(item)) {
            var container = self.createElt(elt, null, params);
            item.forEach(function (secondLevelItem) {
                /** Если secondLevelItem являеться DOM элементом */
                if(secondLevelItem && typeof secondLevelItem === 'object' && !$.isArray(secondLevelItem)) {
                    container.append(secondLevelItem);
                    return;
                }

                /** Если secondLevelItem являеться обычным контентом элемента(пока что это сомнительный поступок) */
                /* container.append(self.createElt(elt, secondLevelItem, params)); */
            });
            tr.append(container);
            return;
        }

        /** Если item являеться обычным контентом элемента */
        tr.append(self.createElt(elt, item, params));
    });
    return tr;
};
/** Метод обрабатывает удаление раскладки */
MicrocicleTrainingAdd.prototype.deletePlanHandler = function(event){
    var self = this;
    var btn = $(event.currentTarget);
    var planId = btn.attr('id');
    var exerciseId = btn.closest('tr').attr('id');
    var renderedItem = this.findRenderedExercise(exerciseId);
    var trainingName = renderedItem = renderedItem.trainingName;
    // btn.closest('tr').empty();
    var oldRenderedItems = this.hasRenderedTrainings;
    this.hasRenderedTrainings = [];
    oldRenderedItems.forEach(function(item){
        if(item && item.exerciseId === exerciseId) {
            var oldPlans = item.trainingPlans;
            item.trainingPlans = [];
            oldPlans.forEach(function(plan){
                if(plan && plan.id === planId) return;
                item.trainingPlans.push(plan);
            });
            self.hasRenderedTrainings.push(item);
        }
        self.hasRenderedTrainings.push(item);
    });
    console.log(this.hasRenderedTrainings);
    this.fullReRender(exerciseId);
    this.renderTotalString(exerciseId);
    this.renterTotalDataOfExercise(trainingName);
};
/** Метод обрабатывает удаление упражнения */
MicrocicleTrainingAdd.prototype.deleteExerciseHandler = function(event){
    var self = this;
    var row = $(event.currentTarget).closest('tr');
    var rowId = row.attr('id');
    var renderedItem = this.findRenderedExercise(rowId);
    var oldExerciseList = this.hasRenderedTrainings;
    this.hasRenderedTrainings = [];
    oldExerciseList.forEach(function(item){
        if(item.exerciseId === rowId) return;
        self.hasRenderedTrainings.push(item);
    });
    row.next().remove();
    row.remove();
    this.renderTotalString(rowId);
    this.renterTotalDataOfExercise(renderedItem.trainingName);
};
/** Метод обрабатывает удаление тренировки */
MicrocicleTrainingAdd.prototype.deleteTrainingHandler = function(event){
    var self = this;
    var elt = $(event.currentTarget).closest('div.basic.segment');
    var trainingId = elt.attr('id'); /** Получаем имя тренировки, оно храниться в качестве атрибута id сегмента */

    /** Пересобираем хранилище тренировок и осеиваем удаляемую тренировку */
    var oldRenderingTrainings = this.hasRenderedTrainings;
    this.hasRenderedTrainings = [];
    oldRenderingTrainings.forEach(function(item){
        if(item.trainingName !== trainingId) self.hasRenderedTrainings.push(item);
    });

    /** Удаляем таблицу которая отображада тренировку из DOM */
    elt.remove();
};

/** Методы для редактирования существующих тренировок */
/**
 * Метод обрабатывает добавление упражнений в тренировку
 * @trainingId - идентификатор тренировки(trainingName)
 */
MicrocicleTrainingAdd.prototype.addExerciseInTraining = function(trainingId){
    if(this.storageExercises && this.storageExercises.length){
        var self = this;
        this.storageExercises.forEach(function(exercise){
            /** Подготавливаем данные для вставки */
            var data = self.prepareDataToSave(exercise, trainingId);
            self.counter++;

            /** Сохраняем данные в хранилище */
            self.hasRenderedTrainings.push({
                'trainingName': trainingId,
                'exerciseName': data.name,
                'exerciseId': data.id,
                'exercisePM': data.pm,
                'trainingPlans': []
            });
        });
        this.resetStartData();
    }
};
/**
 * Метод перередеривает всю таблицу с упражнениями
 * Получает идентификатор упражнения (trainingName)
 */
MicrocicleTrainingAdd.prototype.reRenderExerciseTable = function(trainingName){
    var self = this;
    var container = document.getElementById(trainingName);
    if(!container){
        container = this.createElt('div', null, {
            'class': 'ui basic segment',
            'id': trainingName
        });
        $(this.trainingRender).append(container);
    }
    var renderedExercises = []; // Хранилище идентификаторов упражнения оторые были отрендерены
    container = $(container);
    container.empty();

    /** Inserting header */
    container.append(
        self.createElt('div', trainingName.substring(0, trainingName.indexOf('_')), {'class': 'ui left floated header'}),
        self.createElt('div', 'Удалить', {'class': 'ui right floated basic secondary mini button microcicle_deleteTrainingBtn'}),
        self.createElt('div', null, {'class': 'ui clearing fitted divider'})
    );

    /** Creating table head */
    var thead = this.createElt('thead', null, {
        'children': this.generateTableRow([
            this.createElt('th', '№', {'class': 'collapsing top aligned'}),
            this.createElt('th', 'Упражнение'),
            this.createElt('th', '1 ПМ', {'class': 'collapsing'}),
            this.createElt('th', 'Вес', {'class': 'collapsing'}),
            this.createElt('th', 'Повторения', {'class': 'collapsing'}),
            this.createElt('th', 'Подходы', {'class': 'collapsing'}),
            this.createElt('th', 'КПШ', {'class': 'collapsing'}),
            this.createElt('th', 'Тоннаж', {'class': 'collapsing'}),
            this.createElt('th', 'Ср.Вес', {'class': 'collapsing'}),
            this.createElt('th', 'Отн.инт', {'class': 'collapsing'}),
            this.createElt('th', 'Действия', {
                'class': 'collapsing center aligned'
            })
        ])
    });

    /** Creating table body */
    var tbody = this.createElt('tbody');
    this.hasRenderedTrainings.forEach(function(training){
        if((training && training.trainingName !== trainingName) || !training) return;

        var tr = self.generateTableRow([
            self.createElt('td', null, {
                'class': 'top aligned center aligned',
                'children': [
                    self.createElt('div', null, {'class': 'ui hidden fitted divider'}),
                    self.createElt('div', training.exerciseId.substring(0 ,training.exerciseId.indexOf('_')))
                ]
            }),
            self.createElt('td', null, {
                'class': 'top aligned left aligned',
                'children': self.createElt('div', training.exerciseName)
            }),
            self.createElt('td', null, {
                'class': 'top aligned center aligned',
                'children': self.createElt('div', training.exercisePM)
            }),
            self.createElt('td', null, {
                'class': 'top aligned',
                'children':self.createElt('div', null, {
                    'class': 'ui fluid transparent input',
                    'children': self.createElt('input', null, {
                        'type': 'text',
                        'class': 'training_weight',
                        'value': '0'
                    })
                })
            }),
            self.createElt('td', null, {
                'class': 'top aligned',
                'children': self.createElt('div', null, {
                    'class': 'ui transparent fluid input',
                    'children': self.createElt('input', null, {
                        'class': 'training_repeat',
                        'type': 'text',
                        'value': '0'
                    })
                })
            }),
            self.createElt('td', null, {
                'class': 'top aligned',
                'children': self.createElt('div', null, {
                    'class': 'ui transparent fluid input',
                    'children': self.createElt('input', null, {
                        'class': 'training_repeatSection',
                        'type': 'text',
                        'value': '0'
                    })
                })
            }),
            self.createElt('td', null, {
                'class': 'top aligned center aligned',
                'children': self.createElt('div', '0')
            }),
            self.createElt('td', null, {
                'class': 'top aligned center aligned',
                'children': self.createElt('div', '0')
            }),
            self.createElt('td', null, {
                'class': 'top aligned center aligned',
                'children': [
                    self.createElt('div', null, {'class': 'ui hidden fitted divider'}),
                    self.createElt('div', '0'),
                    self.createElt('div', null, {'class': 'ui hidden fitted divider'})
                ]
            }),
            self.createElt('td', null, {
                'class': 'top aligned center aligned',
                'children': [
                    self.createElt('div', null, {'class': 'ui hidden fitted divider'}),
                    self.createElt('div', '0%'),
                    self.createElt('div', null, {'class': 'ui hidden fitted divider'})
                ]
            }),
            [
                self.createElt('div', null, {
                    'class': 'ui mini very compact icon basic inverted button training_addSubPlan',
                    'children': self.createElt('i', null, {'class': 'ui plus icon'})
                }),
                self.createElt('div', null, {
                    'class': 'ui mini very compact icon basic inverted button training_exerciseDeleteBtn',
                    'children': self.createElt('i', null, {'class': 'ui delete icon'})
                })
            ]
        ], null,'td');

            tr.attr('id', training.exerciseId);
            self.renderPlanToRow(tr);
            tbody.append(tr);
            console.log(tr);

            renderedExercises.push(training.exerciseId);
        });

    /** Creating table footer */
    var tr = this.generateTableRow([
        null,
        'Общие данные',
        null,
        null,
        null,
        null,
        '0',
        '0',
        '0',
        '0',
        null
    ], null, 'th');
    console.log(tr);
    var tfoot = this.createElt('tfoot', null, {
        // 'id': 'totalMonitor' + trainingName,
        'children': tr.attr('id', 'totalMonitor' + trainingName)
    });

    /** Inserting table */
    container.append(
        this.createElt('table', null, {
            'class': 'ui selectable very compact single line inverted celled table',
            'children':[
                thead,
                tbody,
                tfoot
            ]
        })
    );

    /** Manipulation after inserting */
    renderedExercises.forEach(function(item){
        self.renderTotalString(item);
    });
    this.renterTotalDataOfExercise(trainingName);

    /** Reinitialise handlers */
    this.trainingManipulationInit();
};

/**
 * Метод возвращает список тренировок в виде масива объектов
 * name - название тренировки
 * value - идентификатор тренировки(trainingName)
 */
MicrocicleTrainingAdd.prototype.getRegisteredTrainings = function(){
    var tmp = [];
    var result = [];

    this.hasRenderedTrainings.forEach(function(item){
        if(!item) return;

        if(tmp.indexOf(item.trainingName) === -1) {
            tmp.push(item.trainingName);
            result.push({
                'name': item.trainingName.substr(0, item.trainingName.indexOf('_')),
                'value': item.trainingName
            });
        }
    });
    return result;
};

/** Метод загружает данные о микроцикле для редактирования */
MicrocicleTrainingAdd.prototype.loadMicrocicleData = function(){
    return $.ajax({
        url: getAbsoluteUrl('mentor/training/get-microcicle-data'),
        type: 'GET',
        data: {microcicleId: this.container.getAttribute('data-id')},
        success: function(data){
            console.log(data);
        }
    });
};
/** Метод иницыализирует редактирование микроцикла */
MicrocicleTrainingAdd.prototype.initEdit = function(){
    var self = this;

    /** Отрисовываем таблицу */
    this.loadMicrocicleData().then(function(response){
        console.log(response);
        self.hasRenderedTrainings = response.data;
        var trainings = self.getRegisteredTrainings();
        console.log(trainings);
        trainings.forEach(function(item){
            self.reRenderExerciseTable(item.value);
        });
    });

    /** Устанавливаем необходимые переменные */
    this.editMode = true;
    this.editMicrocicleName = this.container.getAttribute('data-name');
};

MicrocicleTrainingAdd.prototype.init = function(){
    /** Временно */
    if(this.container.getAttribute('data-type') === 'edit'){
        this.initEdit();
    }

    var self = this;
    if(this.container && this.selectCheckboxes){
        Array.prototype.forEach.call(this.selectCheckboxes, function(checkbox){
            $(checkbox).checkbox({
                onChecked: function(){
                    var elt = $(this);
                    elt.closest('tr').addClass('active');
                    var container = elt.closest('td');
                    self.storageExercises.push({
                        'name': container.attr('data-name'),
                        'id': container.attr('data-id'),
                        'pm': container.attr('data-pm')
                    });
                },
                onUnchecked: function(){
                    var elt = $(this);
                    elt.closest('tr').removeClass('active');
                    var id = elt.closest('td').attr('data-id');
                    var oldStorage = self.storageExercises;
                    self.storageExercises = [];
                    oldStorage.forEach(function(item){
                        if(item.id === id) return;
                        self.storageExercises.push(item);
                    });
                }
            });
        });
    }

    if(this.container && this.trainingAddBtn){
        $(this.trainingAddBtn).on('click', this.addHandler.bind(this));
    }
};


/** Выбор аты начала микроцикла */
function MicrocicleCalendar(){
    this.yearContainer = document.getElementsByClassName('microcicle_calendarYear')[0];
    this.monthContainer = document.getElementsByClassName('microcicle_calendarMonth')[0];
    this.dayContainer = document.getElementsByClassName('microcicle_calendarDay')[0];

    this.yearRange = 5; // На сколько лет вперед выдавать список

    /** Блок свойств в которых будут храниться данные */
    this.selectedYear = false;
    this.selectedMonth = false;
    this.selectedDay = false;

    var date = new  Date();
    var year = date.getFullYear();
    var month = date.getMonth(); // Нумерация месяцов начинаеться с нуля
    var day = date.getDate();

    this.initYearContainer(year);
    this.initMonthContainer(month);
    this.initDayContainer(day, this.selectedMonth || month);
};
/** Метод возвращает количество дней в месяце */
MicrocicleCalendar.prototype.getCountDay = function(monthName){
     var dayInMonth = {
         'Январь': 31,
         'Февраль': 28,
         'Март': 31,
         'Апрель': 30,
         'Май': 31,
         'Июнь': 30,
         'Июль': 31,
         'Август': 31,
         'Сентябрь': 30,
         'Октябрь': 31,
         'Ноябрь': 30,
         'Декабрь': 31
     };
     return dayInMonth[monthName];
};
/** Метод связывает помер месяца с его названием */
MicrocicleCalendar.prototype.bindNumberWithMonth = function(number){
    var month = [
        'Январь',
        'Февраль',
        'Март',
        'Апрель',
        'Май',
        'Июнь',
        'Июль',
        'Август',
        'Сентябрь',
        'Октябрь',
        'Ноябрь',
        'Декабрь'
    ];
    return month[number];
};
/** Метод наполняет список годов */
MicrocicleCalendar.prototype.initYearContainer = function(currentYear){
    var maxYear = currentYear + this.yearRange;
    var data = [];
    var self = this;
    for(var i = currentYear; i < maxYear; i++){
        data.push({
            'name': i,
            'value': i,
            'selected': currentYear === i,
        });
    }
    $(this.yearContainer).dropdown({
        'values': data,
        'onChange': function(value){
            self.selectedYear = value;
        }
    });
};
/** Метод наполняет список месяцев */
MicrocicleCalendar.prototype.initMonthContainer = function(currentMonth, currentDay){
    var data = [];
    var self =this;
    for(var i = 0; i < 12; i++){
        data.push({
            'name': this.bindNumberWithMonth(i),
            'value': i,
            'selected': i === currentMonth,
            'disabled': i < currentMonth
        });
    }
    $(this.monthContainer).dropdown({
        'values': data,
        'onChange': function(value){
            self.selectedMonth = value;
            if(self.selectedMonth !== false)
                self.initDayContainer(currentDay, self.selectedMonth);
        }
    });
};
/** Метод наполняет список дней */
MicrocicleCalendar.prototype.initDayContainer = function(currentDay, currentMonth){
    var data = [];
    var self = this;
    var maxDay = this.getCountDay(this.bindNumberWithMonth(currentMonth)); /** Получаем количество дней в месяце */
    for(var i = 1; i <= maxDay; i++){
        data.push({
            'name': i,
            'value': i,
            'selected': currentDay ? i === currentDay : false,
            'disabled': currentDay ? i < currentDay : false
        });
    }
    $(this.dayContainer).dropdown({
        'values': data,
        'onChange': function(value){
            self.selectedDay = value;
        }
    });
};


var calendar = document.getElementsByClassName('microcicle_calendar')[0];
var microcicleInit = document.getElementsByClassName('microcicle_init')[0];
var trainingSave = document.getElementsByClassName('microcicle_trainingSave')[0];
/** Иницыализируем выбор даты */
if(calendar){
    var calendarController = new MicrocicleCalendar();
}
/** Иницыализируем компонент управления тренировками */
if(microcicleInit){
    var microcicleController = new MicrocicleTrainingAdd();
    microcicleController.init();
}
/** Отправка данных на сервер */
/**
 * macrocicleId - идентификатор макроцикла
 * dateBegin - дата начала микроцыкла
 * microcicleDuration - продолжительность микроцикла
 * microcicleName - название микроцикла
 * trainingData - данные о тренировках и расладках
 *      trainingName - навание тренировки в формате [name]_[other data]
 *      exerciseName - название упражнения
 *      exerciseId - идентификатор упражнения в формате [count number]_[id]_[other data]
 *      exercisePM - одноповторный максимум в упражнении
 *      trainingPlans - тренровочные планы
 *          id - идентификатор упражнения для которого составляеться раскладка в формате [some number]_[id]_[training name]_[other data]
 *          weight - вес в раскладке
 *          repeat - повторения
 *          repeatSections - подходы
 *          exercisePM - одноповторный максимум
 */
if(trainingSave){
    if(!calendarController || !microcicleController) $(trainingSave).addClass('disabled');

    $(trainingSave).on('click', function(event){
        microcicleController.deleteErrorMessage();

        var macrocicleId = $(this).attr('data-macrocicle-id');
        var date = calendarController;
        if(date.selectedMonth){
            var tmp = parseInt(date.selectedMonth) + 1;
            var month = tmp < 10 ? '0' + tmp : tmp;
        };
        if(date.selectedDay){
            var day = date.selectedDay < 10 ? '0' + date.selectedDay : date.selectedDay;
        };
        var data = {
            'macrocicleId': macrocicleId,
            /** Коректная дата указываеться через слэш в формате YYYY/MM/DD */
            'dateBegin': date.selectedYear + '/' + month + '/' + day,
            'trainingData': microcicleController.hasRenderedTrainings
        };

        var duration = document.getElementsByClassName(('microcicle_microcicleDuration'))[0].value;
        if(!microcicleController.editMode) {
            var microcicleNameField = document.getElementsByClassName('microcicle_microcicleName')[0];
            if (!microcicleNameField) {
                console.log('empty microcicle field');
                return;
            }
            var microcicleName = microcicleNameField.value.trim();
            if (!microcicleName) {
                microcicleController.errorMessage('Имя микроцикла должно быть заполнено');
                return false;
            }
        } else {
            var microcicleName = microcicleController.editMicrocicleName;
        }
        if(!microcicleController.hasRenderedTrainings.length) {
            microcicleController.errorMessage('Отсутсвуют тренировки в микроцикле');
            return false;
        }
        if(microcicleController.hasRenderedTrainings.length){
            var hasError = false;
            microcicleController.hasRenderedTrainings.forEach(function(item){
                if(!item.trainingPlans.length) {
                    microcicleController.errorMessage('Не указана раскладка для упражнения "' + item.exerciseName + '"');
                    hasError = true;
                }
            });
            if(hasError) return false;
        }
        data.microcicleName = microcicleName;
        data.microcicleDuration = duration;
        data.microcicleId = microcicleController.container.getAttribute('data-id');

        var url = microcicleController.editMode ? 'mentor/training/save-edit-microcicle' : 'mentor/training/save-microcicle';

        console.log(data);
        console.log(getAbsoluteUrl(url));
        $.ajax({
            url: getAbsoluteUrl(url),
            type: 'POST',
            data: JSON.stringify(data),
            processData: false, // Этот параметр нужен иначе будут приходить строка [Object object]
            complete: function(response){
                console.log(response.responseJSON);
            }
        });
    });
}

/** Выводит всплывающее меню микроцыкла */
$('.microcicleOperationMenu').on('click', function(){
    var elt = $(this);
    var microcicleId = elt.attr('data-microcicle-id');
    var microcicleName = elt.attr('data-microcicle-name');
    $('#microcicleOperation').html(
        '<div class="header">' + '<div class="ui center aligned inverted dividing header">' + microcicleName + '</div>' + '</div>'+
        '<div class="content">' +
        '<div class="ui basic inverted red button microcicleDeleteBtn" data-id="' + microcicleId + '">Удалить</div>' +
        '<a class="ui inverted right floated button microcicleEditBtn" href="' + getAbsoluteUrl('mentor/training/edit-microcicle?id=' + microcicleId) + '">Редактировать</a>' +
        '</div>'
    ).modal('toggle');
    $('.microcicleDeleteBtn').on('click', function(){
        $.ajax({
            url: getAbsoluteUrl('mentor/training/edit-microcicle'),
            type: 'POST',
            data: microcicleId
        });
    });
});