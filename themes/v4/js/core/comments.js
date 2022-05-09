$(document).ready(function(){
    $('#send_comment_post').click(function(){
        $('#errorMessageComment').addClass('hide');
        // trigger tinymce
        var content = $('#miniEditor').val();
        $.post('/agregar_comentario', {id:$(this).data('id'), content: content}, function(response){
            response = JSON.parse(response);
            if (response.error == false) {
                $('.no-comments-voidloop').hide();
                $('#main_box_comentarios').append('<div class="panel panel-comentario me" id="comentario-MAIN'+response.objeto.id_comentario+'">'+
                        '<a href="/usuario/'+response.objeto.usuario_objeto.id_usuario+'/'+response.objeto.usuario_objeto.nick_seo+'" class="comment-thumbnail"><img src="'+response.objeto.usuario_objeto.avatar+'" title="avatar de '+response.objeto.usuario_objeto.nick+'"></a>'+
                        '<div class="panel-heading"><div class="triangle"></div> <a href="/usuario/'+response.objeto.usuario_objeto.id_usuario+'/'+response.objeto.usuario_objeto.nick_seo+'">'+response.objeto.usuario_objeto.nick+'</a> dijo '+response.objeto.fecha_formateada+': '+
                        '<div class="actions"><div class="action comment-citar" data-id="'+response.objeto.id_comentario+'"><i class="citar"></i></div><div class="action comment-eliminar" data-id="'+response.objeto.id_comentario+'"><i class="eliminar"></i></div><div class="action comment-editar" data-id="'+response.objeto.id_comentario+'"><i class="editar"></i></div></div>'+
                        '</div>'+
                        '<div class="panel-body">'+response.contenido+
                        '</div><div class="hide" id="comentario-ID'+response.objeto.id_comentario+'">'+response.contenido+'</div></div>');
                $('#miniEditor').val('');
            } else {
                // error al comentar
                $('#errorMessageComment').removeClass('hide');
                $('#errorMessageComment').html(response.message);
            }
        })
    });
    $('.emoticono').click(function(){
        //$(this).data('code')
    });
    $('#main_box_comentarios').on('click', '.action.comment-citar', function(){
        //var comentario = $('#comentario-ID'+$(this).data('id')).html();
        executeInsert('[cita='+$(this).data('id')+']', 'El texto es agregado automáticamente[/cita]');
    });
    $('#main_box_comentarios').on('click', '.action.comment-eliminar', function(){
        $.post('/eliminar_comentario', {id:$(this).data('id')}, function(){
        });
        $('#comentario-MAIN'+$(this).data('id')).fadeOut();
    });
    $('#main_box_comentarios').on('click', '.action.comment-like', function(){
        $.post('/votar_comentario', {id:$(this).data('id'), tipo:1}, function(){
        });
        $('#likebutton-'+$(this).data('id')).fadeOut();
        $('#dislikebutton-'+$(this).data('id')).fadeOut();
    });
    $('#main_box_comentarios').on('click', '.action.comment-dislike', function(){
        $.post('/votar_comentario', {id:$(this).data('id'), tipo:2}, function(){
        });
        $('#likebutton-'+$(this).data('id')).fadeOut();
        $('#dislikebutton-'+$(this).data('id')).fadeOut();
    });
    $('#main_box_comentarios').on('click', '.action.comment-editar', function(){
        $('#errorMessageComment').addClass('hide');
        $('.panel-comentario').removeClass('comment-editando');
        $('#comentario-MAIN'+$(this).data('id')).addClass('comment-editando');
        $('#send_comment_post').addClass('hide');
        $('#send_comment_edited').removeClass('hide');
        window.location= '#editCommentPanel';
        $('#send_comment_adv').removeClass('hide');
        $('#miniEditor').val($('#comentario-ID'+$(this).data('id')).html());
        $('#send_comment_edited').data('id', $(this).data('id'));
    });
    $('#send_comment_edited').click(function(){
        var content = $('#miniEditor').val();
        var razon = prompt('Explica la razón de editar el comentario:');
        $.post('/actualizar_comentario', {id:$(this).data('id'), content: content, razon: razon}, function(response){
            response = JSON.parse(response);
            if (response.error == false) {
                location.reload();
            } else {
                showAlert('Ocurrió un error', response.message);
            }
        })
    });
    $('.comment-reset').click(function(){
        $.post('/reiniciar_comentario', {id:$(this).data('id')}, function(){
            location.reload();
        });
    });
});