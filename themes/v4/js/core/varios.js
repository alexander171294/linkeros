var ALERT_GLOBAL_CALLBACK;
var CONFIRM_GLOBAL_CALLBACK;

$(document).ready(function(){
    $('.jsLink').click(function(){
        window.location = $(this).data('target');    
    });
    
    $('.closeModal').click(function(){
        $('#'+$(this).data('modal')).addClass('hide');
        $('.cortina').addClass('hide');
    });
    
    $('#barbtn_logout').click(function(e){
        $.post('/logout', {urequest:true}, function(){
           location.reload(); 
        });
        e.preventDefault();
    });
    
    $('#openEmotiBox').click(function(e){
        $('#emotiBox').toggle();
        e.preventDefault();
        return false;
    });
    
    $('#barbtn_notify').click(function(e){
        e.preventDefault();
        $('#barbtn_notify').addClass('opened');
        $('#barbtn_notify i').addClass('spinner');
        $.get('/notify', function(res){
            res = JSON.parse(res);
            $('#notifybox .uNotifyList').html('');
            for (i =0; i<res.length; i++) {
                if (res[i].tipo_accion == 1) {
                    $('#notifybox .uNotifyList').append('<li><i class="icon seguir"></i> <a href="'+res[i].actor_url+'">'+res[i].actor.nick+'</a> te está siguiendo</li>');
                } else if (res[i].tipo_accion == 2) {
                    $('#notifybox .uNotifyList').append('<li><i class="icon favoritos"></i> <a href="'+res[i].actor_url+'">'+res[i].actor.nick+'</a> agregó a favoritos tu <a href="'+res[i].target_url+'">post</a></li>');
                } else if (res[i].tipo_accion == 3) {
                    $('#notifybox .uNotifyList').append('<li><i class="icon working"></i> <a href="'+res[i].actor_url+'">'+res[i].actor.nick+'</a> eliminó tu comentario <a href="'+res[i].target_url+'">aqui</a></li>');
                } else if (res[i].tipo_accion == 4) {
                    $('#notifybox .uNotifyList').append('<li><i class="icon comentarios"></i> <a href="'+res[i].actor_url+'">'+res[i].actor.nick+'</a> comentó tu <a href="'+res[i].target_url+'">post</a></li>');
                } else if (res[i].tipo_accion == 5) {
                    $('#notifybox .uNotifyList').append('<li><i class="icon comentarios"></i> <a href="'+res[i].actor_url+'">'+res[i].actor.nick+'</a> comentó un <a href="'+res[i].target_url+'">post</a> que sigues</li>');
                } else if (res[i].tipo_accion == 6) {
                    $('#notifybox .uNotifyList').append('<li><i class="icon draft"></i> <a href="'+res[i].actor_url+'">'+res[i].actor.nick+'</a> creó un <a href="'+res[i].target_url+'">post</a></li>');
                } else if (res[i].tipo_accion == 7) {
                    $('#notifybox .uNotifyList').append('<li><i class="icon working"></i> tu <a href="'+res[i].target_url+'">post</a> fué pegado</li>');
                } else if (res[i].tipo_accion == 8) {
                    $('#notifybox .uNotifyList').append('<li><i class="icon working"></i> uno de tus post fué eliminado</li>');
                } else if (res[i].tipo_accion == 9) {
                    $('#notifybox .uNotifyList').append('<li><i class="icon draft"></i> tu <a href="'+res[i].target_url+'">post</a> se encuentra en revisión</li>');
                } else if (res[i].tipo_accion == 10) {
                    $('#notifybox .uNotifyList').append('');
                } else if (res[i].tipo_accion == 11) {
                    $('#notifybox .uNotifyList').append('<li><i class="icon puntos"></i> <a href="'+res[i].actor_url+'">'+res[i].actor.nick+'</a> puntuó <a href="'+res[i].target_url+'">post</a></li>');
                } else if (res[i].tipo_accion == 12) {
                    $('#notifybox .uNotifyList').append('<li><i class="icon puntuar"></i> <a href="'+res[i].actor_url+'">'+res[i].actor.nick+'</a> te recomendó un <a href="'+res[i].target_url+'">post</a></li>');
                } else if (res[i].tipo_accion == 13) {
                    $('#notifybox .uNotifyList').append('<li><i class="icon puntos"></i> nuevo rango adquirido '+res[i].rango+'</li>');
                }
            }
            $('#barbtn_notify i').removeClass('spinner');
            $('#notifybox').removeClass('hide');
        });
        e.stopPropagation();
        return false;
    });
    
    $('#catSelectorMain').change(function(){
        var id = $('#catSelectorMain option:selected').data('id');
        var seo = $('#catSelectorMain option:selected').data('seo');
        window.location = '/categoria/'+id+'/'+seo;
    });
    
    $('#alert_terminar').click(function(){
        $('.cortina').addClass('hide');
        $('#modalAlert').addClass('hide');
        ALERT_GLOBAL_CALLBACK();
    });
    
    $('#confirm_yes').click(function(){
        $('.cortina').addClass('hide');
        $('#modalConfirm').addClass('hide');
        CONFIRM_GLOBAL_CALLBACK(true);
    });
    
    $('#confirm_no').click(function(){
        $('.cortina').addClass('hide');
        $('#modalConfirm').addClass('hide');
        CONFIRM_GLOBAL_CALLBACK(false);
    });
    
    // default closes
    $('body').click(function(){
        $('#barbtn_notify').removeClass('opened');
        $('#notifybox').addClass('hide');
    });
    
    setEvents_userActions();
    setEvents_postActions();
});

function showAlert(title, content, callback) {
    $('.cortina').removeClass('hide');
    $('#modalAlert').removeClass('hide');
    $('#alertTitle').html(title);
    $('#alertContent').html(content);
    var top = window.innerHeight/2-$('#panel-modalAlert').height()/2;
    if (top < 0) top=0;
    $('#panel-modalAlert').css('margin-top', top);
    ALERT_GLOBAL_CALLBACK = callback;
}

function showConfirm(title, content, callback) {
    $('.cortina').removeClass('hide');
    $('#modalConfirm').removeClass('hide');
    $('#confirmTitle').html(title);
    $('#confirmContent').html(content);
    var top = window.innerHeight/2-$('#panel-modalConfirm').height()/2;
    if (top < 0) top=0;
    $('#panel-modalConfirm').css('margin-top', top);
    CONFIRM_GLOBAL_CALLBACK = callback;
}

function setEvents_userActions() {
    $('.groupUserActions').on('click', '.general_boton_seguir', function(){
        $.post('/seguir_usuario', {id:$(this).data('id')}, function(r){
            r = JSON.parse(r);
            if (!r.error) {
                $('.groupUserActions .general_boton_seguir').addClass('general_boton_no_seguir');
                $('.groupUserActions .general_boton_seguir').html('<i class="icon no-seguir"></i> Dejar de seguir');
                $('.groupUserActions .general_boton_seguir').removeClass('general_boton_seguir');
            }
        })
    });
    $('.groupUserActions').on('click', '.general_boton_no_seguir', function(){
        $.post('/no_seguir_usuario', {id:$(this).data('id')}, function(r){
            r = JSON.parse(r);
            if (!r.error) {
                $('.groupUserActions .general_boton_no_seguir').addClass('general_boton_seguir');
                $('.groupUserActions .general_boton_no_seguir').html('<i class="icon seguir"></i> Seguir usuario');
                $('.groupUserActions .general_boton_no_seguir').removeClass('general_boton_no_seguir');
            }
        })
    });
}

function setEvents_postActions() {
    $('.groupPostActions').on('click', '.general_boton_seguir', function(){
        $.post('/seguir_post', {id:$(this).data('id')}, function(r){
            r = JSON.parse(r);
            if (!r.error) {
                $('.groupPostActions .general_boton_seguir').addClass('general_boton_no_seguir');
                $('.groupPostActions .general_boton_seguir').html('<i class="icon no-seguir"></i> Dejar de seguir');
                $('.groupPostActions .general_boton_seguir').removeClass('general_boton_seguir');
            }
        })
    });
    $('.groupPostActions').on('click', '.general_boton_no_seguir', function(){
        $.post('/no_seguir_post', {id:$(this).data('id')}, function(r){
            r = JSON.parse(r);
            if (!r.error) {
                $('.groupPostActions .general_boton_no_seguir').addClass('general_boton_seguir');
                $('.groupPostActions .general_boton_no_seguir').html('<i class="icon seguir"></i> Seguir post');
                $('.groupPostActions .general_boton_no_seguir').removeClass('general_boton_no_seguir');
            }
        })
    });
    $('.general_boton_favorito').click(function(){
        $.post('/favorito_post', {id:$(this).data('id')}, function(r){
            r = JSON.parse(r);
            if (!r.error) {
                $('.groupPostActions .general_boton_favorito').addClass('hide');
            }
        });
    });
    
    $('.openBoxyMiniDialog').click(function(e){
        $('.boxy_mini_dialog').addClass('hide');
        $('#'+$(this).data('id')).removeClass('hide');
        e.stopPropagation();
    });
    $('.boxy_mini_dialog').click(function(e){
        e.stopPropagation();
    });
    $('body').click(function(){
        $('.boxy_mini_dialog').addClass('hide');
    });
}