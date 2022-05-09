
        var TipoBusqueda = 1;
        var opciones_opened = false;
        $('#users').click(function(e){
            activeSearchUsers();
            e.preventDefault();
        });
        $('#tagsbutton').click(function(e){
            activeSearchTags();
            e.preventDefault();
        });
        $('#post').click(function(e){
            TipoBusqueda = 1;
            $('.bigSearcher-background').removeClass('users');
            $('.bigSearcher-logo').removeClass('users');
            $('#postbutton').addClass('active');
            $('#tagsbutton').removeClass('active');
            $('#usersbutton').removeClass('active');
            $('.big-searchbox').removeClass('users');
            $('.big-searchbox').removeClass('tags');
            $('#opciones').show();
            $('.bigSearcher-background').removeClass('tags');
            $('.bigSearcher-logo').removeClass('tags');
            e.preventDefault();
        });
        if (GPT_SEARCH_TYPE == 2) {
            activeSearchUsers();
        }
        if (GPT_SEARCH_TYPE == 3) {
            activeSearchTags();
        }
        
        $('#opciones').click(function(e){
            if (opciones_opened)
            {
              $('#panelOptions').addClass('hide');
              $('.bigSearcher-background').removeClass('open');
            }
            else
            {
              $('.bigSearcher-background').addClass('open');
              $('#panelOptions').removeClass('hide');
            }
            opciones_opened = !opciones_opened;
        });
          
        $('#realizarBusqueda').click(function(){
            
            var formData = document.getElementById('formBusquedaMain');
            
            var text = document.createElement("input"); //input element, text
            text.setAttribute('type',"hidden");
            text.setAttribute('name',"q");
            text.setAttribute('value', $('#textoBusquedaMain').val());
                
            var oTipoBusqueda = document.createElement("input"); //input element, text
            oTipoBusqueda.setAttribute('type',"hidden");
            oTipoBusqueda.setAttribute('name',"searchType");
            oTipoBusqueda.setAttribute('value', TipoBusqueda);
                
            formData.appendChild(text);
            formData.appendChild(oTipoBusqueda);
            
            // opciones especiales
            if (TipoBusqueda == 1 && opciones_opened) {
                var oCategory = document.createElement("input"); //input element, text
                oCategory.setAttribute('type',"hidden");
                oCategory.setAttribute('name',"categoria");
                oCategory.setAttribute('value', $('#filtroCategoria').val());
                formData.appendChild(oCategory);
                var oAutor = document.createElement("input"); //input element, text
                oAutor.setAttribute('type',"hidden");
                oAutor.setAttribute('name',"autor");
                oAutor.setAttribute('value', $('#autor').val());
                formData.appendChild(oAutor);
            } 
            
            formData.submit();
            
        });
        
        function activeSearchUsers() {
            TipoBusqueda = 2;
            $('.bigSearcher-background').addClass('users');
            $('.bigSearcher-background').removeClass('tags');
            $('.bigSearcher-background').removeClass('open');
            $('.bigSearcher-logo').addClass('users');
            $('.bigSearcher-logo').removeClass('tags');
            $('#postbutton').removeClass('active');
            $('#usersbutton').addClass('active');
            $('#tagsbutton').removeClass('active');
            $('.big-searchbox').addClass('users');
            $('.big-searchbox').removeClass('tags');
            $('#panelOptions').addClass('hide');
            opciones_opened = false;
            $('#opciones').hide();
        }
        
        function activeSearchTags() {
            TipoBusqueda = 3;
            $('.bigSearcher-background').addClass('tags');
            $('.bigSearcher-background').removeClass('users');
            $('.bigSearcher-background').removeClass('open');
            $('.bigSearcher-logo').addClass('tags');
            $('.bigSearcher-logo').removeClass('users');
            $('#postbutton').removeClass('active');
            $('#tagsbutton').addClass('active');
            $('#usersbutton').removeClass('active');
            $('.big-searchbox').addClass('tags');
            $('.big-searchbox').removeClass('users');
            $('#panelOptions').addClass('hide');
            opciones_opened = false;
            $('#opciones').hide();
        }