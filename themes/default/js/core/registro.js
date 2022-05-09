var chkpass2Q = false;
$(document).ready(function(){
        $('.core_registro').click(function(){
            showRegistro();
            $('#adv_ingresaNombre').removeClass('hide');
        });
        $('#registro_nick').focus(function(){
            $('#adv_ingresaNombre').addClass('hide');
        });
        $('#registro_nick').change(function(){
            chknick();
        });
        $('#registro_pass').change(function(){
            chkpass1();
        });
        $('#registro_pass2').change(function(){
            chkpass2();
        });
        $('#registro_email').change(function(){
            chkemail();
        });
        $('#registro_dia').change(function(){
            $('#adv_ingresaDia').addClass('hide');
            $('#adv_ingresaMes').removeClass('hide');
        });
        $('#registro_mes').change(function(){
            $('#adv_ingresaMes').addClass('hide');
            $('#adv_ingresaAnio').removeClass('hide');
        });
        $('#registro_anio').change(function(){
            $('#adv_ingresaAnio').addClass('hide');
        });
        
        $('#registro_siguiente').click(function(){
            chknick();
            chkemail();
            if (chkpass1() && chkpass2() && STATUS_NICK && STATUS_EMAIL) {
                $('#registro_step1').addClass('hide');
                $('#registro_step2').removeClass('hide');
            }
        });
        
        $('#registro_gro1').click(function(){
            $('#succ_selectSexo').removeClass('hide');
            $('#adv_ingresaPais').removeClass('hide');
        });
        $('#registro_gro2').click(function(){
            $('#succ_selectSexo').removeClass('hide');
            $('#adv_ingresaPais').removeClass('hide');
        });
        
        $('#registro_pais').change(function(){
            var pais = $('#registro_pais').val();
            $('#adv_ingresaPais').addClass('hide');
            $('#succ_ingresaPais').addClass('hide');
            if (pais>0) {
                $('#succ_ingresaPais').removeClass('hide');
                $('#adv_ingresaRegion').removeClass('hide');
            } else {
                $('#adv_ingresaPais').removeClass('hide');
            }
        });
        
        $('#registro_region').change(function(){
            $('#adv_ingresaRegion').addClass('hide');
            $('#succ_ingresaRegion').removeClass('hide');
            $('#adv_aceptaTerminos').removeClass('hide');
        });
        
        $('#registro_terminos').click(function(){
            chkTerms();
        });
        
        $('#registro_terminar').click(function(){
            if(chkTerms())
            {
                ejecutarRegistro();
            }
        });
    });

function showRegistro() {
    $('.cortina').removeClass('hide');
    $('#modalregistro').removeClass('hide');
    var top = window.innerHeight/2-$('#modalregistro .panel-registro').height()/2;
    if (top < 0) top=0;
    $('.panel-registro').css('margin-top', top);
}
function reshowRegistro() {
    $('#registro_step1').removeClass('hide');
    $('#registro_step2').addClass('hide');
    $('.cortina').removeClass('hide');
    if (GPT_recaptcha) grecaptcha.reset();
}
function showRegistroOk() {
    $('#modalregistro_ok').removeClass('hide');
    var top = window.innerHeight/2-$('#modalregistro_ok .panel-registro').height()/2;
    if (top < 0) top=0;
    $('#modalregistro_ok .panel-registro').css('margin-top', top);
}

var STATUS_NICK = false;
function chknick() {
    $('#err_ingresaNombre').addClass('hide');
    $('#succ_ingresaNombre').addClass('hide');
    $('#work_ingresaNombre').removeClass('hide');
    var nick = $('#registro_nick').val();
    $.post('/check_nick', {nick: nick}, function(response){
        response = JSON.parse(response);
        $('#work_ingresaNombre').addClass('hide');
        if (response.error == true) {
                $('#err_ingresaNombre').removeClass('hide');
                $('#err_ingresaNombre_msg').html(response.message);
                STATUS_NICK = false;
        } else {
                $('#succ_ingresaNombre').removeClass('hide');
                $('#adv_ingresaPass').removeClass('hide');
                STATUS_NICK = true; 
        }
    });
}

function chkpass1() {
     $('#adv_ingresaPass').addClass('hide');
    $('#err_ingresaPass').addClass('hide');
    $('#succ_ingresaPass').addClass('hide');
    var pass = $('#registro_pass').val();
    if (pass.length<8) {
        $('#err_ingresaPass').removeClass('hide');
        if (chkpass2Q) {
            chkpass2();
        }
        return false;
    } else {
        $('#succ_ingresaPass').removeClass('hide');
        if (chkpass2Q) {
            chkpass2();
        } else {
            $('#adv_ingresaPass2').removeClass('hide');
        }
        return true;
    }
}

function chkpass2() {
    chkpass2Q = true;
    $('#adv_ingresaPass2').addClass('hide');
    $('#err_ingresaPass2').addClass('hide');
    $('#succ_ingresaPass2').addClass('hide');
    var pass = $('#registro_pass').val();
    var pass2 = $('#registro_pass2').val();
    if (pass!==pass2) {
        $('#err_ingresaPass2').removeClass('hide');
        return false;
    } else {
        $('#succ_ingresaPass2').removeClass('hide');
        $('#adv_ingresaEmail').removeClass('hide');
        return true;
    }
}

var STATUS_EMAIL = false;
function chkemail() {
    var email = $('#registro_email').val();
    $('#adv_ingresaEmail').addClass('hide');
    $('#err_ingresaEmail').addClass('hide');
    $('#succ_ingresaEmail').addClass('hide');
    $('#work_ingresaEmail').addClass('hide');
    if (!validarEmail(email)) {
        $('#err_ingresaEmail').removeClass('hide');
        $('#err_ingresaEmail_msg').html('El email no es válido');
        return false;
    } else {
        // enviar ajax para verificar si ya está en uso
        $('#succ_ingresaEmail').addClass('hide');
        $('#err_ingresaEmail').addClass('hide');
        $('#work_ingresaEmail').removeClass('hide');
        $.post('/check_email', {email: email}, function(r){
                $('#work_ingresaEmail').addClass('hide');
                r = JSON.parse(r);
                if(r.error == false)
                {
                        $('#succ_ingresaEmail').removeClass('hide');
                        $('#adv_ingresaDia').removeClass('hide');
                        STATUS_EMAIL = true;
                } else {
                        $('#err_ingresaEmail').removeClass('hide');
                        $('#err_ingresaEmail_msg').html(r.message);
                        STATUS_EMAIL = false; 
                }
        }); 
    }
}

function chkTerms() {
    $('#succ_aceptaTerminos').addClass('hide');
    $('#adv_aceptaTerminos').addClass('hide');
    if ($('#registro_terminos').is(':checked')) {
        $('#succ_aceptaTerminos').removeClass('hide');
        return true;
    } else {
        $('#adv_aceptaTerminos').removeClass('hide');
        return false;
    }
}

function validarEmail(email) {
    expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return expr.test(email);
}

function ejecutarRegistro() {
    var nick = $('#registro_nick').val();
    var pass = $('#registro_pass').val();
    var pass2 = $('#registro_pass2').val();
    var email = $('#registro_email').val();
    var dia_n = $('#registro_dia').val();
    var mes_n = $('#registro_mes').val();
    var anio_n = $('#registro_anio').val();
    if (GPT_recaptcha) {
        var v = grecaptcha.getResponse(RegistroRecaptchaRender);
    } else {
        var v = '';
    }
    var genero = -9;
    if ($('#registro_gro1').is(':checked')) 
        genero = 1; // femenino
    if ($('#registro_gro2').is(':checked')) 
        genero = 2; // masculino
    var pais = $('#registro_pais').val();
    var region = $('#registro_region').val();
    
    var tos = $('#registro_terminos').is(':checked');
    $('#registro_terminar').prop('disabled', true);
    $.post('/realizar_registro', {nick:nick, pass:pass, pass2:pass2, email:email, dia: dia_n, mes: mes_n, anio:anio_n, genero: genero, tos:tos, pais:pais, region:region, captcha:v}, function(response){
        response = JSON.parse(response);
        if (response.error == false) {
            //code
            $('#modalregistro').addClass('hide');
            showRegistroOk();
        } else {
            showAlert('error', response.message, function(){
              reshowRegistro()  
            });
        }
        
    });
}