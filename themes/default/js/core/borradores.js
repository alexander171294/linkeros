var GLB_theTarget = null;

$(document).ready(function(){
    
    $('#vaciarBorradores').click(function(){
        showConfirm('Está seguro?', '¿Realmente quiere eliminar todos los post en borradores? esta acción no se puede revertir', function(response){
            if (response) {
                $.post('/borradores/vaciar', {tipo: 1}, function(){
                    location.reload();
                });
            }
        });
    });
    
    
    $('.publicarBorrador').click(function(){
        GLB_theTarget = $(this).data('target');
        showConfirm('Está seguro?', '¿Realmente quiere publicar este borrador?, tenga en cuenta que aparecerá en los listados', function(response){
            if (response) {
                $.post('/borradores/publicar', {idpost: GLB_theTarget}, function(){
                    location.reload();
                });
            }
        });
    });
    
    $('.borrarBorrador').click(function(){
        GLB_theTarget = $(this).data('target');
        showConfirm('Está seguro?', '¿Realmente quiere eliminar este borrador?, esta acción no se puede revertir', function(response){
            if (response) {
                $.post('/borradores/borrar', {idpost: GLB_theTarget}, function(){
                    location.reload();
                });
            }
        });
    });
    
});