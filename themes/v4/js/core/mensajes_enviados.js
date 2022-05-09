var id_BORRADO = 0;
$('#vaciarSPEC').click(function(){
    showConfirm('Está seguro?', '¿Realmente quiere eliminar todos los mensajes enviados?', function(response){
        if (response) {
            $.post('/mensajes/vaciar', {tipo: 2}, function(){
                location.reload();
            });
        }
    });
});
$('.borrarMP').click(function(){
    id_BORRADO = $(this).data('id');
    showConfirm('Está seguro?', '¿Realmente quiere eliminar este mensaje privado?', function(response){
        if (response) {
            $.post('/mensajes/eliminar', {tipo:2, id:id_BORRADO}, function(){
                $('#recordMP-'+id_BORRADO).fadeOut();
            });
        }
    });
});