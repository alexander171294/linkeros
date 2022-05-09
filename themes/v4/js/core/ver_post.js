var IDTARGET_GLOBAL;

$(document).ready(function(){    
    $('#unlockContent').click(function(){
        var id = $('#postInfoBody').data('id');
        var v = grecaptcha.getResponse(PostRecaptchaRender);
        $.post('/block_content', {post: id, captcha: v}, function(r){
            r = JSON.parse(r);
            if(r[0] == "El captcha no es valido")
            {
                showAlert('Oops', "El captcha no es válido");
                grecaptcha.reset(PostRecaptchaRender);
            } else {
                var out = '';
                for(i=0; i<r.length; i++)
                {
                    out += window.atob(r[i])+'<hr/>';
                }
                console.log(out);
                showAlert('Contenido bloqueado', out);
                grecaptcha.reset(PostRecaptchaRender);
            }
        });
    });
    
    // reportar
    $('#post_reportar').click(function(){
        IDTARGET_GLOBAL = $(this).data('id');
        showReport();
    });
    
    $('#report_terminar').click(function(){
        var razon = $('#report_razon').val();
        var message = $('#report_aclaracion').val();
        $('#reportError').addClass('hide');
        $.post('/report', {id_post: IDTARGET_GLOBAL, razon:razon, message:message}, function(response){
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
    
    $('#catalog_add').click(function(){
        IDTARGET_GLOBAL = $(this).data('idpost');
        showConfirm('¿está seguro?', '¿Usted quiere sugerir agregar el post a la sección catálogo?.', function(response){
            if(response)
                $.post('/catalogo_add', {post: IDTARGET_GLOBAL}, function(r){
                    if(r.success)
                    {
                        showAlert('Genial!', 'El post fué sugerido para el catálogo', function(){
                            window.location.reload();
                        });
                    } else {
                        showAlert('Oh no!', 'Ocurrió un error al sugerir el post');
                    }
                }, "JSON");
        });
    });
    
    $('#catalogo_vaciar_sugerencias').click(function(){
        IDTARGET_GLOBAL = $(this).data('idpost');
        showConfirm('¿está seguro?', '¿Usted quiere vaciar las sugerencias del catálogo?.', function(response){
            if(response)
                $.post('/catalogo_void_suggest', {post: IDTARGET_GLOBAL}, function(r){
                    if(r.success)
                    {
                        showAlert('Genial!', 'Al post se le vaciaron las sugerencias', function(){
                            window.location.reload();
                        });
                    } else {
                        showAlert('Oh no!', 'Ocurrió un error al vaciar el post');
                    }
                }, "JSON");
        });
    });
    
    $('#catalogo_vaciar_reportes').click(function(){
        IDTARGET_GLOBAL = $(this).data('idpost');
        showConfirm('¿está seguro?', '¿Usted quiere vaciar los reportes del catálogo?.', function(response){
            if(response)
                $.post('/catalogo_void_reports', {post: IDTARGET_GLOBAL}, function(r){
                    if(r.success)
                    {
                        showAlert('Genial!', 'Al post se le vaciaron los reportes', function(){
                            window.location.reload();
                        });
                    } else {
                        showAlert('Oh no!', 'Ocurrió un error al vaciar el post');
                    }
                }, "JSON");
        });
    });
    
    $('.post_puntuar_action').click(function(e){
        var id = $('.point-bar').data('id');
        var puntos = $(this).data('cantidad');
        $.post('/puntuar_post', {id:id, puntos:puntos}, function(response){
            $('.point-bar').fadeOut();
        });
        e.preventDefault();
    });
    
    $('#catalogo_deletear').click(function(e){
        IDTARGET_GLOBAL = $(this).data('idpost');
        showConfirm('¿está seguro?', '¿Usted quiere eliminar este post de la sección catálogo?.', function(response){
            if(response)
                $.post('/catalogo_delete', {post: IDTARGET_GLOBAL}, function(r){
                    if(r.success)
                    {
                        showAlert('Genial!', 'El post fué eliminado del catálogo', function(){
                            window.location.reload();
                        });
                    } else {
                        showAlert('Oh no!', 'Ocurrió un error al eliminar el post');
                    }
                }, "JSON");
        });
        e.preventDefault();
    });
    
    $('#catalogo_reportar').click(function(){
        IDTARGET_GLOBAL = $(this).data('id');
        showConfirm('¿está seguro?', '¿Usted quiere solicitar que quiten el post del catálogo?.', function(response){
            if(response)
                $.post('/catalogo_report', {post: IDTARGET_GLOBAL}, function(r){
                    if(r.success)
                    {
                        showAlert('Gracias!', 'El post fué reportado. Si quiere reportar el post para que sea removido de la comunidad utilice el botón de reportar post.', function(){
                            $('#catalogo_reportar').remove();
                        });
                    } else {
                        showAlert('Oh no!', 'Ocurrió un error al reportar el post');
                    }
                }, "JSON");
        });
    });
});

function showReport() {
    $('.cortina').removeClass('hide');
    $('#modalReport').removeClass('hide');
    var top = window.innerHeight/2-$('#panel-modalReport').height()/2;
    if (top < 0) top=0;
    $('#panel-modalReport').css('margin-top', top);
}

