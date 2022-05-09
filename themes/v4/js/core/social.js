$('.shareFB').click(function(e){
    e.preventDefault();
    FB.ui(
    {
        method: 'feed',
        name: $(this).data('name'),
        link: $(this).data('link'),
        picture: $(this).data('picture'),
        caption: $(this).data('caption'),
        description: $(this).data('description'),
        message: ''
    });
return false;
});
$('.shareTwitter').click(function(e){
    e.preventDefault();
    var text = encodeURI($(this).data('description')+' - '+$(this).data('link'));
    var realLink = 'https://twitter.com/intent/tweet?original_referer='+encodeURI($(this).data('link'))+'&ref_src=twsrc%5Etfw&text='+text+'&tw_p='+encodeURI($(this).data('link'))+'&via='+$(this).data('via');
    window.open(realLink, 'popup', 'popup');
    return false;
});
var GLB_theTarget = '';
$('.shareLinkeros.activeWork').click(function(e){
    e.preventDefault();
    GLB_theTarget = $(this).data('postid');
    showConfirm('Compartir', 'Esta acción enviará una notificación a todos sus seguidores recomendando el post', function(response){
        if (response) {
            $.post('/linkear_post', {id:GLB_theTarget}, function(){
                $('.shareLinkeros').addClass('disabled');
                $('.shareLinkeros').prop('disabled', true);
            });
        }
    });
    return false;
});

window.fbAsyncInit = function() {
    FB.init({
        appId : FB_APP_ID,
        status : true, // check login status
        cookie : true, // enable cookies to allow the server to access the session
        xfbml : true // parse XFBML
    });
};
         
(function() {
    var e = document.createElement('script');
    e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
    e.async = true;
    document.getElementById('fb-root').appendChild(e);
}());