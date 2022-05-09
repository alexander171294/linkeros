// mensajes privados
var ORIGIN_MP_RESPONSE = 0;
if (CONST_AUTOCOMPLETE_PARTIALS) {
    $( "#mp_nick" ).autocomplete({
        source: function (request, response) {
            $('#mp_nick_spinner').removeClass('hide');
            $.post("/mensajes/nick", {
                query: request.term
            }, function (data) {
                $('#mp_nick_spinner').addClass('hide');
                response(JSON.parse(data));
            });
        },
        minLength: 3
    });
}

$('.sendMP').click(function(){
    openMPBox($(this).data('to'));
});
$('#mp_cancelar').click(function(){
    closeMPBox();
});
$('#mp_enviar').click(function(){
    hideErrorMP();
    checkSendMP();
});
$('#vaciarMP').click(function(){
    showConfirm('Está seguro?', '¿Realmente quiere eliminar todos los mensajes enviados y recibidos?', function(response){
        if (response) {
            $.post('/mensajes/vaciar', {tipo: 3}, function(){
                location.reload();
            });
        }
    });
});

function openMPBox(to, origin = 0) {
    ORIGIN_MP_RESPONSE = origin;
    $('.cortina').removeClass('hide');
    $('#modalMP').removeClass('hide');
    $('#mp_nick').val(to);
    var top = window.innerHeight/2-$('#panel-modalMP').height()/2;
    if (top < 0) top=0;
    $('#panel-modalMP').css('margin-top', top);
}

function closeMPBox() {
    $('.cortina').addClass('hide');
    $('#modalMP').addClass('hide');
    $('#mp_nick').val('');
    $('#mp_asunto').val('');
    $('#mp_mensaje').val('');
    $('#mp_sended').addClass('hide');
    $('#mp_error').addClass('hide');
    $('#mp_enviar').removeAttr('disabled');
    $('#panelmpContent').removeClass('hide');
}

function checkSendMP() {
    $('#mp_asunto').removeClass('invalid');
    $('#mp_mensaje').removeClass('invalid');
    
    if($('#mp_asunto').val().length<3){
        showErrorMP('Debes completar el asunto');
        $('#mp_asunto').addClass('invalid');
        return false;
    }
    if($('#mp_mensaje').val().length<5){
        showErrorMP('Debes completar el mensaje');
        $('#mp_mensaje').addClass('invalid');
        return false;
    }
    
    $('#mp_enviar').attr('disabled', 'disabled');
    // chequear validez de usaurio
    $.post('/mensajes/nuevo', {nick: $('#mp_nick').val(), asunto: $('#mp_asunto').val(), mensaje: $('#mp_mensaje').val(), origin: ORIGIN_MP_RESPONSE}, function(response){
        response = JSON.parse(response);
        if (response.error == false) {
            $('#mp_sended').removeClass('hide');
            $('#panelmpContent').addClass('hide');
            setTimeout(function(){closeMPBox();}, 1000);
        } else {
            showErrorMP(response.message);
            $('#mp_enviar').removeAttr('disabled');
        }
    });
}

function showErrorMP(msg) {
    $('#mp_error').html(msg);
    $('#mp_error').removeClass('hide');
}

function hideErrorMP(msg) {
    $('#mp_error').addClass('hide');
}