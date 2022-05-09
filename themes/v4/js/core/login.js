var errorCount = 0;
$(document).ready(function(){
    $('#core_login').click(function(e){
        $('#loginbox').removeClass('hide');
        $('#clogin_action').addClass('opened');
        e.stopPropagation();
    });
    $('#loginbox').click(function(e){
        e.stopPropagation();    
    });
    $(document).click(function(){
        $('#loginbox').addClass('hide');
        $('#clogin_action').removeClass('opened');
    });
    
    var loginFN = function(){
        var nick = $('#login_user').val();
        var pass = $('#login_pass').val();
        if(typeof grecaptcha !== 'undefined')
            var captcha = grecaptcha.getResponse(LoginRecaptchaRender);
        $('#login_error').addClass('hide');
        $.post('/ajax_login', {nick:nick, pass:pass, captcha: captcha}, function(response){
            response = JSON.parse(response);
            if (response.error == false) {
                location.reload();
            } else {
                if(response.message == "El captcha no es valido" || response.message == "Debe ingresar el captcha") $('#captchaInLogin').css('display', 'block');
                $('#login_error').html(response.message);
                $('#login_error').removeClass('hide');
                errorCount++;
                if(errorCount>MaxLoginAttemp)
                {
                    $('#captchaInLogin').css('display', 'block');
                }
            }
        });
    };
    
    $('#login_button').click(loginFN);
    
    $('#login_pass').keyup(function(r){
        if(r.keyCode == 13)
            loginFN();
    });
    
    $('#login_user').keyup(function(r){
        if(r.keyCode == 13)
            loginFN();
    });
    
    $('#remember_terminar').click(function(){
        $(this).prop('disabled', true);
        $.post('/new_data', {email:$('#remember_email').val()}, function(){
            showAlert('Enviamos un email', 'Enviamos un email con sus nuevos datos, revise la bandeja de correo no deseado (spam)', function(){
                hideRemember();
            });
        }, "JSON");
    });
    
    $('#login_remember').click(function(e){
        showRemember();
        e.preventDefault();
    });
});

function showRemember() {
    $('.cortina').removeClass('hide');
    $('#modalremember').removeClass('hide');
    var top = window.innerHeight/2-$('#modalremember .panel-registro').height()/2;
    if (top < 0) top=0;
    $('.panel-registro').css('margin-top', top);
}
function hideRemember() {
    $('.cortina').addClass('hide');
    $('#modalremember').addClass('hide');
}