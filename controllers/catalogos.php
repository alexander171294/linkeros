<?php

_::define_autocall(function(){
    _::$view->assign('section', 'catalogos');
    _::$view->assign('subsection', null);
    if(!SECCION_CATALOGO){
        _::redirect('/closed', false);
        die();
    }
});


_::define_controller('catalogos', function(){
    _::$view->assign('subsection', 'inicio');
    
    $cate = isset(_::$get['categoria']) ? _::$get['categoria']->int() : 0;
    $idioma = isset(_::$get['idioma']) ? _::$get['idioma']->int() : 0;
    $calidad = isset(_::$get['calidad']) ? _::$get['calidad']->int() : 0;
    $pagina = isset(_::$get['page']) ? _::$get['page']->int() : 0;
    
    _::$view->assign('categorias_catalog', categorias::getAllObjects('id_categoria', 'WHERE in_catalog = 1'));
    _::$view->assign('idiomas', catalogo_idiomas::getAllObjects('id_idioma'));
    
    if($cate > 0)
    {
        _::$view->assign('calidades', catalogo_calidades::getAllObjects('id_calidad', 'WHERE id_categoria = ?', array($cate)));
    }
    
    _::$view->assign('stat_cate', $cate);
    _::$view->assign('stat_idioma', $idioma);
    _::$view->assign('stat_calidad', $calidad);
    _::$view->assign('stat_pagina', $pagina);
    
    $action = false; 
    $where = 'WHERE ';
    if($cate > 0) { $where .= 'id_categoria = '.$cate.' '; $action = true; }
    if($idioma > 0) { if($action){ $where .= 'AND '; } $where .= 'id_idioma = '.$idioma.' '; $action = true;}
    if($calidad > 0) { if($action){ $where .= 'AND '; } $where .= 'id_calidad = '.$calidad.' '; $action = true;}
    $realWhere = null;
    if($action) $realWhere = $where;
    
    if($pagina > 0)
    {
        $pagina *= CATALOGO_OBJECTS_PERPAGE;
        $limit = $pagina.','.CATALOGO_OBJECTS_PERPAGE;
    } else $limit = '0,'.CATALOGO_OBJECTS_PERPAGE;
    
    // hay más páginas
    $total = catalogo_objeto::count('id_objeto', $realWhere.'ORDER BY id_objeto DESC');
    _::$view->assign('more_pages', round($total/CATALOGO_OBJECTS_PERPAGE) > $pagina);
    
    _::$view->assign('most_reported', catalogo_reportes::getMostReported(CATALOGO_CANTIDAD_REPORTES));
    _::$view->assign('most_suggested', catalogo_sugerencias::getBests(CATALOGO_CANTIDAD_REPORTES));
    
    _::$view->assign('objects', catalogo_objeto::getAllObjects('id_objeto', $realWhere.'ORDER BY id_objeto DESC LIMIT '.$limit));
    
    _::$view->show('catalogos');
});

_::define_controller('catalogos_descargables', function(){
    _::$view->assign('subsection', 'descargable');
    
    $cate = isset(_::$get['categoria']) ? _::$get['categoria']->int() : 0;
    $idioma = isset(_::$get['idioma']) ? _::$get['idioma']->int() : 0;
    $calidad = isset(_::$get['calidad']) ? _::$get['calidad']->int() : 0;
    $pagina = isset(_::$get['page']) ? _::$get['page']->int() : 0;
    
    _::$view->assign('categorias_catalog', categorias::getAllObjects('id_categoria', 'WHERE in_catalog = 1 AND is_online = 0'));
    _::$view->assign('idiomas', catalogo_idiomas::getAllObjects('id_idioma'));
    
    if($cate > 0)
    {
        _::$view->assign('calidades', catalogo_calidades::getAllObjects('id_calidad', 'WHERE id_categoria = ?', array($cate)));
    }
    
    _::$view->assign('stat_cate', $cate);
    _::$view->assign('stat_idioma', $idioma);
    _::$view->assign('stat_calidad', $calidad);
    _::$view->assign('stat_pagina', $pagina);
    
    _::$view->assign('most_reported', catalogo_reportes::getMostReported(CATALOGO_CANTIDAD_REPORTES));
    _::$view->assign('most_suggested', catalogo_sugerencias::getBests(CATALOGO_CANTIDAD_REPORTES));
    
    $action = false; 
    $where = 'WHERE is_online = 0 ';
    if($cate > 0) $where .= 'AND id_categoria = '.$cate.' ';
    if($idioma > 0) $where .= 'AND id_idioma = '.$idioma.' ';
    if($calidad > 0) $where .= 'AND id_calidad = '.$calidad.' ';
    $realWhere = $where;
    
    if($pagina > 0)
    {
        $pagina *= CATALOGO_OBJECTS_PERPAGE;
        $limit = $pagina.','.CATALOGO_OBJECTS_PERPAGE;
    } else $limit = '0,'.CATALOGO_OBJECTS_PERPAGE;
    
    // hay más páginas
    $total = catalogo_objeto::count('id_objeto', $realWhere.'ORDER BY id_objeto DESC');
    _::$view->assign('more_pages', round($total/CATALOGO_OBJECTS_PERPAGE) > $pagina);
    
    _::$view->assign('objects', catalogo_objeto::getAllObjects('id_objeto', $realWhere.'ORDER BY id_objeto DESC LIMIT '.$limit));
    
    _::$view->show('catalogos');
});

_::define_controller('catalogos_online', function(){
    _::$view->assign('subsection', 'online');
    
    $cate = isset(_::$get['categoria']) ? _::$get['categoria']->int() : 0;
    $idioma = isset(_::$get['idioma']) ? _::$get['idioma']->int() : 0;
    $calidad = isset(_::$get['calidad']) ? _::$get['calidad']->int() : 0;
    $pagina = isset(_::$get['page']) ? _::$get['page']->int() : 0;
    
    _::$view->assign('categorias_catalog', categorias::getAllObjects('id_categoria', 'WHERE in_catalog = 1 AND is_online = 1'));
    _::$view->assign('idiomas', catalogo_idiomas::getAllObjects('id_idioma'));
    
    if($cate > 0)
    {
        _::$view->assign('calidades', catalogo_calidades::getAllObjects('id_calidad', 'WHERE id_categoria = ?', array($cate)));
    }
    
    _::$view->assign('stat_cate', $cate);
    _::$view->assign('stat_idioma', $idioma);
    _::$view->assign('stat_calidad', $calidad);
    _::$view->assign('stat_pagina', $pagina);
    
    _::$view->assign('most_reported', catalogo_reportes::getMostReported(CATALOGO_CANTIDAD_REPORTES));
    _::$view->assign('most_suggested', catalogo_sugerencias::getBests(CATALOGO_CANTIDAD_REPORTES));
    
    $action = false; 
    $where = 'WHERE is_online = 1 ';
    if($cate > 0) $where .= 'AND id_categoria = '.$cate.' ';
    if($idioma > 0) $where .= 'AND id_idioma = '.$idioma.' ';
    if($calidad > 0) $where .= 'AND id_calidad = '.$calidad.' ';
    $realWhere = $where;
    
    if($pagina > 0)
    {
        $pagina *= CATALOGO_OBJECTS_PERPAGE;
        $limit = $pagina.','.CATALOGO_OBJECTS_PERPAGE;
    } else $limit = '0,'.CATALOGO_OBJECTS_PERPAGE;
    
    // hay más páginas
    $total = catalogo_objeto::count('id_objeto', $realWhere.'ORDER BY id_objeto DESC');
    _::$view->assign('more_pages', round($total/CATALOGO_OBJECTS_PERPAGE) > $pagina);
    
    _::$view->assign('objects', catalogo_objeto::getAllObjects('id_objeto', $realWhere.'ORDER BY id_objeto DESC LIMIT '.$limit));
    
    _::$view->show('catalogos');
});

_::define_controller('catalogo_add', function(){
    $idpost = _::$post['post']->int();
    $post = new post($idpost);
    if(!$post->void && isset(_::$globals['me']))
    {
        $sugerencia = new catalogo_sugerencias();
        $sugerencia->id_usuario = _::$globals['me']->id_usuario;
        $sugerencia->id_post = $post->id_post;
        $sugerencia->id_objeto = 0;
        $sugerencia->save();
        _::$globals['me']->puntos_catalogo += 1;
        _::$globals['me']->puntos_obtenidos += 1;
        _::$globals['me']->save();
        _::$view->ajax(array('success' => true));
        die();
    }
    _::$view->ajax(array('success' => false));
});

_::define_controller('catalogo_added', function(){
    if(!_::$globals['me']->isMod() && _::$globals['me']->id_rango !== 6) {
        _::redirect('/',false);
        die();
    }
    _::$view->assign('post', new post(_::$get['page']->int()));
    _::$view->show('catalogo_added');
});

_::define_controller('catalogo_delete', function(){
    if(!_::$globals['me']->isMod() && (CATALOGO_ISMODERABLE_BY_USER && _::$globals['me']->id_rango !== CATALOGO_USER_RANGE)) {
        _::redirect('/',false);
        die();
    }
    $post = new post(_::$post['post']->int());
    if(!catalogo_objeto::isInCatalog($post->id_post, $post->o_categoria))
    {
        _::$view->ajax(array('success' => false));
    } else {
        if($post->o_categoria->is_multiple)
        {
            catalogo_index_multiple::deleteAll('WHERE id_post = ?', array($post->id_post));
            catalogo_reportes::deleteAll('WHERE id_post = ?', array($post->id_post));
            $user = new usuarios($post->id_usuario);
            $user->post_catalogo--;
            $user->save();
            _::$view->ajax(array('success' => 'success'));
        } else
        {
            $obj = catalogo_objeto::getObject($post->id_post, $post->o_categoria);
            catalogo_reportes::deleteAll('WHERE id_post = ?', array($post->id_post));
            $user = new usuarios($post->id_usuario);
            $user->post_catalogo--;
            $user->save();
            $obj->delete();
            _::$view->ajax(array('success' => true));
        }
    }
});

_::define_controller('catalogo_aprove', function(){
    if(!_::$globals['me']->isMod() && (CATALOGO_ISMODERABLE_BY_USER && _::$globals['me']->id_rango !== CATALOGO_USER_RANGE)) {
        _::redirect('/',false);
        die();
    }
    
    $post = new post(_::$get['page']->int());
    _::$view->assign('post', $post);
    if(catalogo_objeto::isInCatalog($post->id_post, $post->o_categoria))
    {
        _::$view->show('catalogo_prev');
    } else {
        _::$view->assign('error_title', false);
        _::$view->assign('error_foto', false);
        _::$view->assign('error_objeto', false);
        _::$view->assign('error_tempo', false);
        _::$view->assign('error_cap', false);
        _::$view->assign('step', 1);
        if(_::$isPost && !$post->o_categoria->is_multiple)
        {
            try{
                if(_::$post['titulo']->len()<3) { $eSection = 'error_title'; throw new Exception('Minimo 3 caracteres'); }
                if(!_::$post['foto']->isLink()) { $eSection = 'error_foto'; throw new Exception('Debe ser un link'); }
                $obj = new catalogo_objeto();
                $obj->is_multiple = $post->o_categoria->is_multiple;
                $obj->is_online = $post->o_categoria->is_online;
                $obj->titulo = (string)_::$post['titulo'];
                $obj->foto = (string)_::$post['foto'];
                $obj->id_idioma = _::$post['idioma']->int();
                $obj->id_calidad = _::$post['calidad']->int();
                $obj->id_categoria = $post->o_categoria->id_categoria;
                $obj->id_post = $post->id_post;
                $obj->save();
                catalogo_sugerencias::deleteAll('WHERE id_post = ?', array($post->id_post));
                $user = new usuarios($post->id_usuario);
                $user->puntos_catalogo += CATALOGO_APROVE_PUNTOS_AUTOR;
                $user->puntos_obtenidos += CATALOGO_APROVE_PUNTOS_AUTOR;
                $user->aportes_catalogo += CATALOGO_APROVE_APORTES_AUTOR;
                $user->post_catalogo++;
                $user->save();
                _::$globals['me']->aportes_catalogo += CATALOGO_APROVE_APORTES_MOD;
                _::$globals['me']->puntos_catalogo += CATALOGO_APROVE_PUNTOS_MOD;
                _::$globals['me']->puntos_obtenidos += CATALOGO_APROVE_PUNTOS_MOD;
                _::$globals['me']->save();
                _::redirect('/catalogo_added/'.$post->id_post, false);
                die();
            } catch(Exception $e) {
               _::$view->assign($eSection, $e->getMessage());
            }
        } else if(_::$isPost && $post->o_categoria->is_multiple){
            try{
                // step 1
                if(isset(_::$post['search']))
                {
                    if(_::$post['search']->len()<3) { $eSection = 'error_title'; throw new Exception('Minimo 3 caracteres'); }
                    _::declare_component('searcher');
                    $search = new Buscador((string)_::$post['search']);
                    $q = $search->getQuerys();
                    if(!empty($q))
                    {
                        foreach($q as $uQ)
                        {
                            $out = catalogo_objeto::getAll('WHERE lower(titulo) LIKE ? AND is_multiple = 1 LIMIT '.SEARCHER_LIMIT, array(strtolower($uQ)));
                            $search->merge($out);
                            $results = $search->filterQuerys('id_post', SEARCHER_LIMIT);
                            if(count($results) > SEARCHER_LIMIT) break; // si superamos los 10 resultados no hay necesidad de seguir
                        }
                    }
                     $results = _::factory($results, 'id_objeto', 'catalogo_objeto');
                    
                    _::$view->assign('searchResult', $results);
                    _::$view->assign('searchCantResult', count($results));
                    _::$view->assign('step', 2);
                }
                
                // step 2
                if(isset(_::$post['existente']))
                {
                    if(_::$post['temporada']->int()<1) { $eSection = 'error_tempo'; throw new Exception('debes ingresar la temporada'); }
                    if(_::$post['capitulo']->int()<1) { $eSection = 'error_cap'; throw new Exception('debes ingresar el capitulo'); }
                    // crear nuevo objeto
                    if(_::$post['existente']->int() == 0)
                    {
                        if(_::$post['titulo']->len()<3) { $eSection = 'error_title'; throw new Exception('Minimo 3 caracteres'); }
                        if(!_::$post['foto']->isLink()) { $eSection = 'error_foto'; throw new Exception('Debe ser un link'); }
                        $obj = new catalogo_objeto();
                        $obj->is_multiple = 1;
                        $obj->is_online = $post->o_categoria->is_online;
                        $obj->titulo = (string)_::$post['titulo'];
                        $obj->foto = (string)_::$post['foto'];
                        $obj->id_idioma = _::$post['idioma']->int();
                        $obj->id_calidad = _::$post['calidad']->int();
                        $obj->id_categoria = $post->o_categoria->id_categoria;
                        $obj->id_post = $post->id_post;
                        $ido = $obj->save();
                    } else {
                        $objeto = new catalogo_objeto(_::$post['existente']->int());
                        $ido = $objeto->id_objeto;
                    }
                    
                    $index = new catalogo_index_multiple();
                    $index->id_objeto = $ido;
                    $index->id_post = $post->id_post;
                    $index->capitulo = _::$post['capitulo']->int();
                    $index->temporada = _::$post['temporada']->int();
                    $index->save();
                    
                    catalogo_sugerencias::deleteAll('WHERE id_post = ?', array($post->id_post));
                    $user = new usuarios($post->id_usuario);
                    $user->puntos_catalogo += CATALOGO_APROVE_PUNTOS_AUTOR;
                    $user->puntos_obtenidos += CATALOGO_APROVE_PUNTOS_AUTOR;
                    $user->aportes_catalogo += CATALOGO_APROVE_APORTES_AUTOR;
                    $user->post_catalogo++;
                    $user->save();
                    _::$globals['me']->aportes_catalogo += CATALOGO_APROVE_APORTES_MOD;
                    _::$globals['me']->puntos_catalogo += CATALOGO_APROVE_PUNTOS_MOD;
                    _::$globals['me']->puntos_obtenidos += CATALOGO_APROVE_PUNTOS_MOD;
                    _::$globals['me']->save();
                    _::redirect('/catalogo_added/'.$post->id_post, false);
                    die();
                }
                
            } catch(Exception $e) {
               _::$view->assign($eSection, $e->getMessage());
            }
        }
        _::$view->assign('idiomas', catalogo_idiomas::getAllObjects('id_idioma'));
        _::$view->assign('calidades', catalogo_calidades::getAllObjects('id_calidad', 'WHERE id_categoria = ?', array($post->o_categoria->id_categoria)));
        _::$view->show('catalogo_aprove');
    }
});

_::define_controller('catalogo_report', function(){
    $reporte = new catalogo_reportes(array(_::$globals['me']->id_usuario, _::$post['post']->int()));
    if($reporte->void)
    {
        $reporte = new catalogo_reportes();
        $reporte->id_post = _::$post['post']->int();
        $reporte->id_usuario = _::$globals['me']->id_usuario;
        $reporte->fecha = time();
        $reporte->save();
        _::$globals['me']->puntos_catalogo += CATALOGO_REPORT_PUNTOS_MOD;
        _::$globals['me']->puntos_obtenidos += CATALOGO_REPORT_PUNTOS_MOD;
        _::$globals['me']->save();
        _::$view->ajax(array('success' => true));
    } else {
        _::$view->ajax(array('success' => false));
    }
    
});

_::define_controller('catalogo_objeto', function(){
    $objeto = new catalogo_objeto(_::$get['page']->int());
    if($objeto->is_multiple)
    {
        _::$view->assign('objeto', $objeto);
        $temporadas = catalogo_index_multiple::getTemporadas($objeto);
        $tout = array();
        foreach($temporadas as $numero)
        {
            $tout[$numero] = catalogo_index_multiple::getCaps($objeto, $numero);
        }
        _::$view->assign('categoria', new categorias($objeto->id_categoria));
        _::$view->assign('temporadas', $tout);
        _::$view->assign('tempos', count($tout));
        $caps = 0;
        foreach($tout as $tcaps)
        {
            $caps += count($tcaps);
        }

        _::$view->assign('caps', $caps);
        _::$view->show('catalogo_multiple');
    } else {
        $post = new post($objeto->id_post);
        _::redirect('/posts/'.$post->id_post.'/'.$post->o_categoria->nombre_seo.'/'.$post->titulo_seo, false);
        die();
    }
});


_::define_controller('catalogo_void_suggest', function(){
    try
    {
        if(!_::$globals['me']->isMod() && (CATALOGO_ISMODERABLE_BY_USER && _::$globals['me']->id_rango !== CATALOGO_USER_RANGE)) throw new Exception('Access deneid');
        catalogo_sugerencias::deleteAll('WHERE id_post = ?', array(_::$post['post']->int()));
        _::$view->ajax(array('success' => true));
    } catch(Exception $e)
    {
        _::$view->ajax(array('success' => false, 'msg' => $e->getMessage()));
    }
});

_::define_controller('catalogo_void_reports', function(){
    try
    {
        if(!_::$globals['me']->isMod() && (CATALOGO_ISMODERABLE_BY_USER && _::$globals['me']->id_rango !== CATALOGO_USER_RANGE)) throw new Exception('Access deneid');
        catalogo_reportes::deleteAll('WHERE id_post = ?', array(_::$post['post']->int()));
        _::$view->ajax(array('success' => true));
    } catch(Exception $e)
    {
        _::$view->ajax(array('success' => false, 'msg' => $e->getMessage()));
    }
});