<?php class_exists('_') || die('FORBIDDEN');

// controlador principal de busquedas (recibe busquedas del home y de la sección buscador)
_::define_controller('buscador', function(){
    // la sección buscador está cerrada?
    if(!SECCION_BUSCADOR){
        _::redirect('closed');
        return false;
    }
    // asignamos la sección/subsección
    _::$view->assign('section', 'buscador');
    _::$view->assign('subsection', null);
    _::$view->assign('searchText', null);
    _::$view->assign('searchBy', 1);
    // enviamos un formulario?
    if(_::$isPost)
    {
        // la busqueda
        _::$view->assign('searchText', (string)_::$post['q']);
        // el tipo de busqueda 1: posts, 2: usuarios, 3: tags
        $st = isset(_::$post['searchType']) ? _::$post['searchType']->int() : 1;
        // asignamos el tipo de busqueda
        _::$view->assign('searchType', $st);
        
        $t = $st;
        // si buscamos un tag
        if($t == 3)
        {
            // si la query es > a 1 caracter
            if(_::$post['q']->len() > 1)
            {
                // buscamos el tag y construimos los objetos
                $tags = _::factory(tags::getBySearch((string)_::$post['q']),'id_tag','tags');
                // asignamos los resultados
                _::$view->assign('searchResult', $tags);
                _::$view->assign('searchCantResult', count($tags));
            } else {
                _::$view->assign('searchCantResult', 0);
            }
            _::$view->assign('searchBy', 3);
        } elseif($t == 2) { // si buscamos un usuario
            // si la query es > a 1 caracter
            if(_::$post['q']->len() > 1)
            {
                // buscamos el usuario y construimos los objetos usuarios
                $usuarios = _::factory(usuarios::getBySearch((string)_::$post['q']),'id_usuario','usuarios');
                // asignamos los resultados
                _::$view->assign('searchResult', $usuarios);
                _::$view->assign('searchCantResult', count($usuarios));
            } else {
                // no van a haber resultados
                _::$view->assign('searchCantResult', 0);
            }
            _::$view->assign('searchBy', 2);
        } elseif($t == 1) { // buscar post
            if(_::$post['q']->len() > 1) // si hay más de 1 caracter
            {
                // obtenemos el componente buscador
                _::declare_component('searcher');
                // generamos la busqueda con la query
                $search = new Buscador((string)_::$post['q']);
                // obtenemos las consultas
                $q = $search->getQuerys();
                // si hay al menos una consulta posible
                if(!empty($q))
                {
                    // preparamos los append para la clausula where
                    $addCat = null;
                    $addAutor = null;
                    // si se seleccionó una categoría
                    if(isset(_::$post['categoria']) && _::$post['categoria']->int() > 0)
                        $addCat = ' AND categoria = '._::$post['categoria']->int(); // preparamos el and
                    // si se seleccionó un autor
                    if(isset(_::$post['autor']) && _::$post['autor']->len() > 0)
                    {
                        // buscar autor y obtener id
                        $id = usuarios::get_by_nick((string)_::$post['autor']);
                        // si existe
                        if(!empty($id) && $id !== false) 
                        {
                            // preparamos el and para la clausula where de busqueda
                            $addAutor = ' AND id_usuario = '.$id;
                        }
                    }
                    
                    // recorremos las consultas que haya
                    foreach($q as $uQ)
                    {
                        // obtenemos los post que cumplan la consulta y los filtros
                        $out = post::getAll('WHERE lower(titulo) LIKE ?'.$addCat.$addAutor.' LIMIT '.SEARCHER_LIMIT, array(strtolower($uQ)));
                        // unimos los resultados que encontramos a los de las consultas previas
                        $search->merge($out);
                        // filtramos los post por su id
                        $results = $search->filterQuerys('id_post', SEARCHER_LIMIT);
                        // si ya llegamos al maximo de resultados por página
                        if(count($results) > SEARCHER_LIMIT) break; // si superamos los 10 resultados no hay necesidad de seguir
                    }
                    // construimos los post correspondientes
                    $results = _::factory($results, 'id_post', 'post');
                    // asignamos los resultados
                    _::$view->assign('searchResult', $results);
                    _::$view->assign('searchCantResult', count($results));
                    // si no habían consultas no hay resultados
                } else _::$view->assign('searchCantResult', 0);
                // si no hay más de 1 caracter en la query de busqueda, no hay resultados
            } else _::$view->assign('searchCantResult', 0);
        }
    }
    // cargamos la plantilla correspondiente
    _::$view->show('buscador');
});