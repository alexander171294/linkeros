<?php class_exists('_') || die('FORBIDDEN');

// Controlador de buscador de catálogo
_::define_controller('catalogo_buscador', function(){
    // la sección buscador está deshabilitada?
    if(!SECCION_BUSCADOR){
        _::redirect('closed');
        return false;
    }
    
    // sección y subsección
    _::$view->assign('section', 'catalogos');
    _::$view->assign('subsection', 'inicio');
    
    // establecemos los filtros
    $cate = isset(_::$post['category']) ? _::$post['category']->int() : 0;
    $idioma = isset(_::$post['idioma']) ? _::$post['idioma']->int() : 0;
    $calidad = isset(_::$post['calidad']) ? _::$post['calidad']->int() : 0;
    
    // obtenemos las categorías por el filtro
    _::$view->assign('categorias_catalog', categorias::getAllObjects('id_categoria', 'WHERE in_catalog = 1'));
    // obtenemos los idiomas disponibles
    _::$view->assign('idiomas', catalogo_idiomas::getAllObjects('id_idioma'));
    
    // hay una categoría seleccionada?
    if($cate > 0)
    {
        // obtenemos las calidades disponibles para esa categoría
        _::$view->assign('calidades', catalogo_calidades::getAllObjects('id_calidad', 'WHERE id_categoria = ?', array($cate)));
    }
    
    // creamos las clausulas where de los filtros
    $andClausule = null;
    if($cate > 0)
        $andClausule .= ' AND id_categoria = '.$cate;
    if($idioma > 0)
        $andClausule .= ' AND id_idioma = '.$idioma;
    if($calidad > 0)
        $andClausule .= ' AND id_calidad = '.$calidad;
    
    // asignamos a la vista los filtros seleccionados
    _::$view->assign('stat_cate', $cate);
    _::$view->assign('stat_idioma', $idioma);
    _::$view->assign('stat_calidad', $calidad);
    // el paginado no está disponible en el buscador
    _::$view->assign('stat_pagina', 0);

    // obtenemos el componente buscador
    _::declare_component('searcher');
    // instanciamos el componente
    $search = new Buscador((string)_::$post['search']);
    // obtenemos las querys basadas en el analisis de la frase
    $q = $search->getQuerys();
    $results = array();
    // si hay querys para buscar
    if(!empty($q))
    {
        // buscamos una por una
        foreach($q as $uQ)
        {
            // obtenemos los objetos que cumplan esa query
            $out = catalogo_objeto::getAll('WHERE lower(titulo) LIKE ?'.$andClausule.' LIMIT '.SEARCHER_LIMIT, array(strtolower($uQ)));
            // unimos a resultados de consultas anteriores
            $search->merge($out);
            // limpiamos las repetidas usando la key id_post
            $results = $search->filterQuerys('id_post', SEARCHER_LIMIT);
            // si ya tenemos más de los resultados máximos por pagina terminamos el bucle
            if(count($results) > SEARCHER_LIMIT) break; // si superamos los SEARCHER_LIMIT resultados no hay necesidad de seguir
        }
    }
    // creamos los catalogos basados en los resultados de las busquedas
    $results = _::factory($results, 'id_objeto', 'catalogo_objeto');
                    
    // guardamos todos los resultados
    _::$view->assign('more_pages', false);
    _::$view->assign('objects', $results);
    _::$view->assign('searchCantResult', count($results));
    
    // obtenemos cosas para moderar
    _::$view->assign('most_reported', catalogo_reportes::getMostReported(CATALOGO_CANTIDAD_REPORTES));
    _::$view->assign('most_suggested', catalogo_sugerencias::getBests(CATALOGO_CANTIDAD_REPORTES));
    
    // cargamos la plantilla principal
    _::$view->show('catalogos');
});