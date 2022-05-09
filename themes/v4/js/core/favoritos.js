var GLB_theTarget = null;

$(document).ready(function(){
    
    
    $('#vaciarFavoritos').click(function(){
        showConfirm('Está seguro?', '¿Realmente quiere eliminar todos los post en favoritos? esta acción no se puede revertir', function(response){
            if (response) {
                $.post('/favoritos/vaciar', {tipo: 1}, function(){
                    location.reload();
                });
            }
        });
    });
    
    $('.quitarFavorito').click(function(){
        var GLB_theTarget = $(this).data('target');
        showConfirm('Está seguro?', '¿Realmente quiere eliminar este post de favoritos? esta acción no se puede revertir', function(response){
            if (response) {
                $.post('/favoritos/delete', {idpost: GLB_theTarget}, function(){
                    location.reload();
                });
            }
        });
    });
    
    $('.borrarPost').click(function(){
        GLB_theTarget = $(this).data('target');
        // reutilizamos el controlador borradores
        showConfirm('Está seguro?', '¿Realmente quiere eliminar este borrador?, esta acción no se puede revertir', function(response){
            if (response) {
                $.post('/borradores/borrar', {idpost: GLB_theTarget}, function(){
                    location.reload();
                });
            }
        });
    });
    
});