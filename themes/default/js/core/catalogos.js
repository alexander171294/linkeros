$(document).ready(function(){
    
    $('#globalContentCatalog').on('mousemove', '.catalogBlock', function(event){
        var x = event.clientX;
        var y = event.clientY;
        $('#catTipsyCli').removeClass('oculto');
        $('#catTipsyCli').css('top', y+10);
        $('#catTipsyCli').css('left', x+20);
        $('#catTipsyCli').html($(this).data('title'));
    });
    $('#globalContentCatalog').on('mouseleave', '.catalogBlock', function(event){
        $('#catTipsyCli').addClass('oculto');
    });
    $('#categoryFilter').change(function(){
        openPage($(this).val(), $('#mainContainerCatalog').data('idioma'), $('#mainContainerCatalog').data('calidad'));
    });
    $('#idiomaFilter').change(function(){
        openPage($('#mainContainerCatalog').data('cate'), $(this).val(), $('#mainContainerCatalog').data('calidad'));
    });
    $('#calidadFilter').change(function(){
        openPage($('#mainContainerCatalog').data('cate'), $('#mainContainerCatalog').data('idioma'), $(this).val());
    });
});

function openPage(categoria, idioma, calidad)
{
    window.location='/catalogos/cat'+categoria+'-i'+idioma+'-c'+calidad+'-p0';
}