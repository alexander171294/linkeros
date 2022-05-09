$(document).ready(function(){
    $('#pickExist').change(function(){
        if($(this).val() == 0)
            $('.nuevoObjeto').prop('disabled', false);
        else
            $('.nuevoObjeto').prop('disabled', true);
    });
});