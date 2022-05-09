var idUSUARIO = 0;
$(document).ready(function(){
    
    $('#borrar_post').click(function(e){ 
        var id = $(this).data('id');
        showConfirm('Estas seguro?', 'Si eliminas este post, será borrado de la base de datos, esto es irreversible.', function(response){
            if(response)
            $.post('/delete_post', {id:id}, function(response){
                response = JSON.parse(response);
                if (response.status == true) {
                    showAlert('Excelente, post eliminado!', 'El post fué eliminado y haz ganado <b>'+(response.pts+1)+' punto</b> de moderación');
                    $.post('/control/revised', {id:$('#ignore_report').data('id')}, function(){
                        
                    });
                } else {
                    showAlert('Problemas de permisos', 'Oops ocurrió un error al hacer esto, verifica que estas logueado.', function(){});
                }
            }, "JSON");
        });
        e.preventDefault();                       
    });
    
    $('#moveto_draft').click(function(e){
        
        var id = $(this).data('id');
        $.post('/make_draft', {id:id}, function(response){
            response = JSON.parse(response);
            if (response.status == true) {
                showAlert('Excelente, post eliminado!', 'El post fué movido a borrador y haz ganado <b>'+(response.pts+1)+' punto</b> de moderación, te recomendamos aprovechar para enviar un mensaje privado al usuario informando tu acción y ganar más puntos.');
                $.post('/control/revised', {id:$('#ignore_report').data('id')}, function(){
                    
                });
            } else {
                showAlert('Problemas de permisos', 'Oops ocurrió un error al hacer esto, verifica que estas logueado.', function(){});
            }
        }, "JSON");
        // solicitamos pasar el post a borrador
        e.preventDefault();
        
    });
    
    $('#next_report').click(function(){
        window.location = '/control/tour/'+$(this).data('page');
    });
    
    $('#ignore_report').click(function(){
        showConfirm('Estas seguro?', 'Si ignoras este reporte, será ignorado por todos los moderadores y borrado de la lista, si solo quieres saltearlo presiona unicamente el boton siguiente y cancela esta acción', function(response){
            if(response)
            $.post('/control/revised', {id:$('#ignore_report').data('id'), assignpts: 1}, function(res){
                showAlert('Excelente!', 'El reporte será ignorado por el sistema, haz ganado <b>1 punto</b> de moderación');
            }, "JSON");
        });   
    });
    
    // PENDIENTES
    $('#ignore_report_user').click(function(){
        showConfirm('Estas seguro?', 'Si ignoras este reporte, será ignorado por todos los moderadores y borrado de la lista, si solo quieres saltearlo presiona unicamente el boton siguiente y cancela esta acción', function(response){
            if(response)
            $.post('/control/reviseduser', {id:$('#ignore_report_user').data('id'), assignpts: 1}, function(res){
                showAlert('Excelente!', 'El reporte será ignorado por el sistema, haz ganado <b>'+res.pts+' punto</b> de moderación');
            }, "JSON");
        });   
    });
    
    $('.bannear_user').click(function(){
        // task
        idUSUARIO = $(this).data('id');
        showConfirm('está seguro?', 'Usted está seguro que quiere bannear este usuario?', function(response){
            if (response) {
                var motivo = prompt("Ingrese el motivo para mostrarle al usuario");
                $.post('/bannear_usuario', {idu: idUSUARIO, motivo: motivo}, function(res){
                    showAlert('Banneado', 'Usuario banneado correctamente. ganaste '+(res.pts+1)+' puntos de moderación');
                    $.post('/control/reviseduser', {id:$('#ignore_report_user'), assignpts: 1}, function(res){}, "JSON");
                }, "JSON");
            }
        });
    });
    $('#ignore_report_mp').click(function(){
        showConfirm('Estas seguro?', 'Si ignoras este reporte, será ignorado por todos los moderadores y borrado de la lista, si solo quieres saltearlo presiona unicamente el boton siguiente y cancela esta acción', function(response){
            if(response)
            $.post('/control/revisedmp', {id:$('#ignore_report_mp').data('id'), assignpts: 1}, function(res){
                showAlert('Excelente!', 'El reporte será ignorado por el sistema, haz ganado <b>'+res.pts+' punto</b> de moderación');
            }, "JSON");
        });   
    });
    
});