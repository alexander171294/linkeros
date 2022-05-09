$(document).ready(function(){
    
    $('.miniEditor-button').mouseenter(function(){
        $('#descriptionBar').removeClass('hide');
        $('#descriptionBar').html($(this).data('description'));
        $('#descriptionBar').css('margin-left', $(this).data('margin'));
    });
    $('.miniEditor-button').mouseleave(function(){
        $('#descriptionBar').addClass('hide');
    });
    
    $('#sizesSelect').change(function(){
        var size = $('#sizesSelect').val();
        BBCodeOpen = '[size='+size+'px]';
        BBCodeClose = '[/size]';
        executeInsert(BBCodeOpen, BBCodeClose);
        $('#miniEselectSize').addClass('hide');
    });
    
    $('#colorSelect').change(function(){
        var color = $('#colorSelect').val();
        BBCodeOpen = '[color='+color+']';
        BBCodeClose = '[/color]';
        executeInsert(BBCodeOpen, BBCodeClose);
        $('#miniEselectColor').addClass('hide');
    });
    
    $('#fuenteSelect').change(function(){
        var color = $('#fuenteSelect').val();
        BBCodeOpen = '[font='+color+']';
        BBCodeClose = '[/font]';
        executeInsert(BBCodeOpen, BBCodeClose);
        $('#miniEselectFuente').addClass('hide');
    });
    
    $('.miniEditor-button').click(function(){
        var bbc = $(this).data('bbc');
        BBCodeOpen = '';
        BBCodeClose = '';
        if (bbc == 'b') {
            BBCodeOpen = '[b]';
            BBCodeClose = '[/b]';
        } else if (bbc == 'i') {
            BBCodeOpen = '[i]';
            BBCodeClose = '[/i]';
        } else if (bbc == 'u') {
            BBCodeOpen= '[u]';
            BBCodeClose = '[/u]';
        } else if (bbc == 'alignL') {
            BBCodeOpen = '[align=left]';
            BBCodeClose = '[/align]';
        } else if (bbc == 'alignC') {
            BBCodeOpen = '[align=center]';
            BBCodeClose = '[/align]';
        } else if (bbc == 'alignR') {
            BBCodeOpen = '[align=right]';
            BBCodeClose = '[/align]';
        } else if (bbc == 'size') {
            // menu aquí
            $('#'+$(this).data('submenu')).removeClass('hide');
            $('#sizesSelect').val(-1);
            return false;
        } else if (bbc == 'color') {
            // menu aquí
            $('#'+$(this).data('submenu')).removeClass('hide');
            $('#colorSelect').val(-1);
            return false;
        } else if (bbc == 'font') {
            // menu aquí
            $('#'+$(this).data('submenu')).removeClass('hide');
            $('#fuenteSelect').val(-1);
            return false;
        } else if (bbc == 'img') {
            // menu aquí
            var link = prompt('ingrese el link de la imagen');
            if (link != null) {
                BBCodeOpen = '[img]'+link;
                BBCodeClose = '[/img]';
            }
        } else if (bbc == 'url') {
            var link = prompt('ingrese el link');
            if (link != null) {
                BBCodeOpen = '[url='+link+']';
                BBCodeClose = '[/url]';
            }
        } else if (bbc == 'code') {
            BBCodeOpen = '[code]';
            BBCodeClose = '[/code]';
        } else if (bbc == 'youtube') {
            // menu aquí
            var link = prompt('ingrese el del video de youtube');
            if (link != null)
            {
                BBCodeOpen = '[youtube]'+link;
                BBCodeClose = '[/youtube]';
            }
        } else if (bbc == 'quote') {
            // menu aquí
            var link = prompt('ingrese el numero de comentario');
            if (link != null)
            {
                BBCodeOpen = '[cita='+link+']La cita se autocompletará automáticamente';
                BBCodeClose = '[/cita]';
            }
        } else if (bbc == 'locked') {
            BBCodeOpen = '[locked]';
            BBCodeClose = '[/locked]';
        }
        
        executeInsert(BBCodeOpen, BBCodeClose);

    });
    
    $('.EmotiBox .emoticonoGLB').click(function(){
        insertUniBlock('('+$(this).data('bbcode')+')');
    });
    
});

function insertUniBlock(bbcode)
{
    var txtarea = document.getElementById("miniEditor");
    var start = txtarea.selectionStart;
    var finish = txtarea.selectionEnd;
    var originalText = $("#miniEditor").val();
    var first = originalText.substring(0,start);
    var end = originalText.substring(start,originalText.length);
    $("#miniEditor").val(first+bbcode+end);
}

function executeInsert(BBCodeOpen, BBCodeClose) {
        var txtarea = document.getElementById("miniEditor");
        var start = txtarea.selectionStart;
        var finish = txtarea.selectionEnd;
        
        var originalText = $("#miniEditor").val();
        if (start != finish) { // hay una selección
            console.log(originalText);
            var first = originalText.substring(0,start);
            var middle = BBCodeOpen+originalText.substring(start,finish)+BBCodeClose;
            var last = originalText.substring(finish, originalText.length);
            $("#miniEditor").val(first+middle+last);
        } else if(start>0) {
            var first = originalText.substring(0,start);
            var middle = BBCodeOpen+BBCodeClose;
            var last = originalText.substring(start, originalText.length);
            $("#miniEditor").val(first+middle+last);
        } else {
            $("#miniEditor").val($("#miniEditor").val()+BBCodeOpen+BBCodeClose);
        }
}