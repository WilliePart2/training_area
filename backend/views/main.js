$('.ui.dropdown').dropdown({
    'on': 'hover'
});

$('document').ready(function(){
    $('.ui.checkbox').checkbox();
});

/**
 * Собственные дополнения
 */
/* Удаляем группу упражнения */
$('.delete_group').on('click', function(){
    var groupId = $(this).attr('data-id');
    $.ajax({
        'data': {
            'id': groupId
        },
        'type': 'POST',
        'url': location. protocol + '//' + location.host + '/' + 'group-exercise/delete-group',
        'complete': function(arguments){
            console.log(arguments);
            var success = arguments.responseJSON.message;
            console.log(success);
            if(success) {
                location.reload(location.href);
            } else {
                alert('Удаление не удалось');
            }
        }
    })
});
