    var TipoBusqueda = 1;
    $('#home-buscador-post').click(function(e){
        $('#home-buscador-li-usuario').removeClass('active');
        $('#home-buscador-li-post').addClass('active');
        $('#home-buscador-li-tags').removeClass('active');
        $('.panel-searcher').removeClass('green');
        $('.panel-searcher').addClass('blue');
        $('.panel-searcher').removeClass('sky');
        TipoBusqueda = 1;
        e.preventDefault();
    });
    $('#home-buscador-usuario').click(function(e){
        $('#home-buscador-li-usuario').addClass('active');
        $('#home-buscador-li-post').removeClass('active');
        $('#home-buscador-li-tags').removeClass('active');
        $('.panel-searcher').removeClass('blue');
        $('.panel-searcher').addClass('green');
        $('.panel-searcher').removeClass('sky');
        TipoBusqueda = 2;
        e.preventDefault();
    });
    $('#home-buscador-tags').click(function(e){
        $('#home-buscador-li-tags').addClass('active');
        $('#home-buscador-li-post').removeClass('active');
        $('#home-buscador-li-usuario').removeClass('active');
        $('.panel-searcher').removeClass('blue');
        $('.panel-searcher').removeClass('green');
        $('.panel-searcher').addClass('sky');
        TipoBusqueda = 3;
        e.preventDefault();
    });
    $('#formBusquedaMain').submit(function(e){
        var formData = document.getElementById('formBusquedaMain');
                  
        var tipoBusqueda = document.createElement("input"); //input element, text
        tipoBusqueda.setAttribute('type',"hidden");
        tipoBusqueda.setAttribute('name',"searchType");
        tipoBusqueda.setAttribute('value', TipoBusqueda);
                
        formData.appendChild(tipoBusqueda);
            
        formData.submit();
        e.preventDefault();
    });
    $('#realizarBusqueda').click(function(){
        var formData = document.getElementById('formBusquedaMain');
                  
        var tipoBusqueda = document.createElement("input"); //input element, text
        tipoBusqueda.setAttribute('type',"hidden");
        tipoBusqueda.setAttribute('name',"searchType");
        tipoBusqueda.setAttribute('value', TipoBusqueda);
                
        formData.appendChild(tipoBusqueda);
            
        formData.submit(); 
    });