$(document).ready(function(){
    $('.profileDataHead').click(function(){
        $('.profileDataHead').addClass('closed');
        $(this).removeClass('closed');
        $('.profileDataGroup .row').css('display', 'none');
        $('#'+$(this).data('tab')).css('display', 'block');
    });
});