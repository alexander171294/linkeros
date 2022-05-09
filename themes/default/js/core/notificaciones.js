if (GRAL_Notificaciones.length > 0) {
    var LastIndexNotificacion = 1;
    $('#news_bar').html('<span id="news_bar_new">'+GRAL_Notificaciones[0]+'</span>');
    $('#news_bar').removeClass('hide');
    setInterval(function()
    {
        if (LastIndexNotificacion == GRAL_Notificaciones.length) {
            LastIndexNotificacion = 0;
        }
        
        $('#news_bar_new').fadeOut('slow', function(){
                $('#news_bar_new').html(GRAL_Notificaciones[LastIndexNotificacion]);
                LastIndexNotificacion++;
                $('#news_bar_new').fadeIn();
            });
        
    },6000);
}
