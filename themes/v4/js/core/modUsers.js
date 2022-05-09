var idUSUARIO = 0;
$('#rango_usuario').change(function(){
    
    $.post('/cambiar_rango_usuario', {rango: $(this).val(), idu: $(this).data('id')}, function(){
        showAlert('Rango actualizado', 'rango actualizado satisfactoriamente.');
    });
});

$('#userEdit').click(function(){
    window.location='/uac_profile/'+$(this).data('id');    
});

$('#userDrop').click(function(){
    idUSUARIO = $(this).data('id');
    showConfirm('está seguro?', 'Si vacía la actividad todos los post, comentarios y mensajes de este usuario serán eliminados (Irreversiblemente)', function(response){
        if(response)
            $.post('/uac_drop', {idu: idUSUARIO}, function(){
                showAlert('Vaciado', 'Toda la actividad de este usuario fué eliminada.');
            });
    });
});

$('#userBan').click(function(){
    idUSUARIO = $(this).data('id');
    showConfirm('está seguro?', 'Usted está seguro que quiere bannear este usuario?', function(response){
        if (response) {
            var motivo = prompt("Ingrese el motivo para mostrarle al usuario");
            $.post('/bannear_usuario', {idu: idUSUARIO, motivo: motivo}, function(){
                showAlert('Banneado', 'Usuario banneado correctamente.');
            });
        }
    });
});
$('#userUnban').click(function(){
    idUSUARIO = $(this).data('id');
    showConfirm('está seguro?', 'Usted está seguro que quiere desbannear este usuario?', function(response){
        if (response) {
            $.post('/desbannear_usuario', {idu: idUSUARIO}, function(){
                showAlert('Genial!', 'Este usuario ya puede acceder nuevamente a la comunidad.');
            });
        }
    });
});