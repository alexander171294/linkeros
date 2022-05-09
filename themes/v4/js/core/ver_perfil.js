$(document).ready(function(){
    $('.reportarUsuario').click(function(){
        IDTARGET_GLOBAL = $(this).data('id');
        showReport();
    });
    
    $('ul.profile-tabs li:not(.specialMenuTab)').click(function(e){
        $('ul.profile-tabs li').removeClass('active');
        $(this).addClass('active');
        $('.blockinfo-profile .row .col-md-8').hide();
        $('#tab_'+$(this).data('tab')).show();
        e.preventDefault();
    });
    
    $('ul.profile-tabs li.specialMenuTab').click(function(e){
        e.preventDefault();
    });
});

$('#report_user_terminar').click(function(){
    var razon = $('#report_razon').val();
    var message = $('#report_aclaracion').val();
    $('#reportError').addClass('hide');
    $.post('/report_users', {id_user: IDTARGET_GLOBAL, razon:razon, message:message}, function(response){
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
