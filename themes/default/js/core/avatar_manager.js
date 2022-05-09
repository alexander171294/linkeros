$(document).ready(function(){
    $('#avatar_manager_link').click(function(){
        var link = prompt('Ingrese el link');
        if (typeof link !== 'object') {
            $.post('/account/avatar', {type:1, link:link}, function(result){
                result = JSON.parse(result);
                if (result.error == true) {
                    alert(result.message);
                } else {
                    location.reload();
                }
            })
        }
    });
    
    $('#avatar_manager_uploader').click(function(e){

        $.ajax( {
            url: '/account/avatar',
            type: 'POST',
            data: new FormData( $('#avatarFormUploader')[0] ),
            processData: false,
            contentType: false,
            success: function(result){
                result = JSON.parse(result);
                if (result.error == true) {
                    alert(result.message);
                } else {
                    location.reload();
                }
            }
          });
        e.preventDefault();
    });
});