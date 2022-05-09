var GLB_theTarget = null;

$(document).ready(function(){

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