$(document).ready(function(){
    
    
    $('.mod_actions#draft').click(function(e){
        
        var id = $(this).data('id');
        $.post('/make_draft', {id:id}, function(response){
            response = JSON.parse(response);
            if (response.status == true) {
                showAlert('Post en borrador', 'Este post fue enviado a borradores, ahora nadie podrá verlo con excepcion de ti.', function(){});
            } else {
                showAlert('Problemas de permisos', 'Oops tu no puedes enviar a borrrador éste post.', function(){});
            }
        });
        // solicitamos pasar el post a borrador
        e.preventDefault();
        
    });
    
    $('.mod_actions#unDraft').click(function(e){
        
        var id = $(this).data('id');
        $.post('/make_undraft', {id:id}, function(response){
            response = JSON.parse(response);
            if (response.status == true) {
                showAlert('Post publico', 'Este post fue publicado, ahora todos podrán verlo.', function(){});
            } else {
                showAlert('Problemas de permisos', 'Oops tu no puedes cambiar éste post.', function(){});
            }
        });
        // solicitamos pasar el post a borrador
        e.preventDefault();
        
    });
    
    $('.mod_actions#unSuspend').click(function(e){
        
        var id = $(this).data('id');
        $.post('/make_unsuspend', {id:id}, function(response){
            response = JSON.parse(response);
            if (response.status == true) {
                showAlert('Post publico', 'Este post fue publicado, ahora todos podrán verlo.', function(){});
            } else {
                showAlert('Problemas de permisos', 'Oops tu no puedes cambiar éste post.', function(){});
            }
        });
        // solicitamos pasar el post a borrador
        e.preventDefault();
        
    });
    
    $('.mod_actions#edit').click(function(e){
        window.location = '/edit_post/'+$(this).data('id');
        e.preventDefault();
    });
    
    $('.mod_actions#delete').click(function(e){ 
        var id = $(this).data('id');
        $.post('/delete_post', {id:id}, function(response){
            response = JSON.parse(response);
            if (response.status == true) {
                showAlert('Post eliminado', 'Este post fue eliminado completamente, este cambio es irreversible.', function(){
                    window.location = '/';
                });
            } else {
                showAlert('Problemas de permisos', 'Oops tu no puedes eliminar éste post.', function(){});
            }
        });                       
        e.preventDefault();                       
    });
    $('.mod_actions#sticky').click(function(e){
        var id = $(this).data('id');
        $.post('/sticky_post', {id:id}, function(response){
            response = JSON.parse(response);
            if (response.status == true) {
                showAlert('Post pegado', 'Este post fue pegado, ahora siempre aparecerá arriba de todo en la lista de post.', function(){});
            } else {
                showAlert('Problemas de permisos', 'Oops tu no puedes pegar éste post.', function(){});
            }
        });                       
        e.preventDefault();
    });
    $('.mod_actions#unSticky').click(function(e){
        var id = $(this).data('id');
        $.post('/unsticky_post', {id:id}, function(response){
            response = JSON.parse(response);
            if (response.status == true) {
                showAlert('Post despegado', 'Este post fue despegado, será como otro post en la lista de post.', function(){});
            } else {
                showAlert('Problemas de permisos', 'Oops tu no puedes despegar éste post.', function(){});
            }
        });                       
        e.preventDefault();
    });
    
    $('.mod_actions#banip').click(function(e){
        var id = $(this).data('id');
        
        showConfirm('está seguro?', 'Si usted realiza esta acción, el usuario no podrá ni siquiera visualizar el sitio.', function(response){
            if(response)
                $.post('/banip', {id:id}, function(response){
                    response = JSON.parse(response);
                    if (response.status == true) {
                        showAlert('Banneadisimo', 'A partir de este momento el usuario no podrá acceder al sitio ni verlo.', function(){});
                    } else {
                        showAlert('Problemas de permisos', 'Oops tu no puedes bannear la ip.', function(){});
                    }
                });
        });
        e.preventDefault();
    });
});

