var id_BORRADO = 0;
var IDTARGET_GLOBAL = 0;
$('.borrarMP').click(function(){
    id_BORRADO = $(this).data('id');
    showConfirm('Está seguro?', '¿Realmente quiere eliminar este mensaje privado?', function(response){
        if (response) {
            $.post('/mensajes/eliminar', {tipo:1, id:id_BORRADO}, function(){
                window.location = '/mensajes';
            });
        }
    });
});

$('.reportarMP').click(function(){
    IDTARGET_GLOBAL = $(this).data('id');
    showReport();
});

$('#responderMP').click(function(){
    openMPBox($(this).data('to'), $(this).data('origin'));
});

$('#report_mp_terminar').click(function(){
    var razon = $('#report_razon').val();
    var message = $('#report_aclaracion').val();
    $('#reportError').addClass('hide');
    $.post('/report_mp', {id_mp: IDTARGET_GLOBAL, razon:razon, message:message}, function(response){
        response = JSON.parse(response);
        if (response.error == false) {
            $('#reportButtons').addClass('hide');
            $('#reportContent').hide();
            $('#reportThanks').removeClass('hide');
            setTimeout(function(){
                $('.cortina').addClass('hide');
                $('#modalReport').addClass('hide');
                $('#reportContent').show();
                $('#reportButtons').removeClass('hide');
                $('#reportThanks').addClass('hide');
            }, 1000);
        } else {
            $('#reportError').html(response.message);
            $('#reportError').removeClass('hide');
        }
    });
});


function showReport() {
    $('.cortina').removeClass('hide');
    $('#modalReport').removeClass('hide');
    var top = window.innerHeight/2-$('#panel-modalReport').height()/2;
    if (top < 0) top=0;
    $('#panel-modalReport').css('margin-top', top);
}
