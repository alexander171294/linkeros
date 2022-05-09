<?php

_::define_autocall(function(){
    if(!isset(_::$globals['me'])) {
        _::redirect('/',false);
        die();
    }
});

_::define_controller('account', function(){
    _::$view->assign('section', null);
    _::$view->assign('subsection', 'account');
    _::$view->assign('error', false);
    _::$view->assign('errorObject', null);
    _::$view->assign('saved', false);
    
    _::$view->assign('paises', _::factory(paises::getAll(), 'id_pais', 'paises'));
    
    $me = _::$globals['me'];
    _::$view->assign('nombre', $me->nombre);
    _::$view->assign('Apais', $me->pais);
    _::$view->assign('region', $me->region);
    _::$view->assign('sexo', $me->sexo);
    
    _::$view->assign('dia_nac', $me->dia_n);
    _::$view->assign('mes_nac', $me->mes_n);
    _::$view->assign('anio_nac', $me->anio_n);
    
    $meses = array('void', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
    unset($meses[0]);
    _::$view->assign('meses', $meses);
    
    if(_::$isPost)
    {
        _e::set('EDIT_ACCOUNT_NOMBRE', 'El nombre no es valido');
        _e::set('EDIT_ACCOUNT_EMAIL', 'El email no es valido');
        _e::set('EDIT_ACCOUNT_PAIS', 'El pais no es valido');
        _e::set('EDIT_ACCOUNT_REGION', 'La region no es valida');
        _e::set('EDIT_ACCOUNT_SEXO', 'El genero elegido no es valido');
        _e::set('EDIT_ACCOUNT_FECHA_NAC', 'La fecha de nacimiento no es valida');
        _::$view->assign('nombre', (string)_::$post['nombre']);
        _::$view->assign('Apais', _::$post['pais']->int());
        _::$view->assign('region', (string)_::$post['region']);
        _::$view->assign('sexo', _::$post['sexo']->int());
        
        _::$view->assign('dia_nac', _::$post['dia']->int());
        _::$view->assign('mes_nac', _::$post['mes']->int());
        _::$view->assign('anio_nac', _::$post['anio']->int());
        try{
            if(_::$post['nombre']->len()>0)
            {
                if(_::$post['nombre']->len()<4) throw new Exception(_e::get('EDIT_ACCOUNT_NOMBRE'));
                $me->nombre = (string)_::$post['nombre'];
            }
            if(_::$post['pais']->int() < 1) throw new Exception(_e::get('EDIT_ACCOUNT_PAIS'));
            $me->pais = _::$post['pais']->int();
            if(_::$post['region']->len()>0)
            {
                if(_::$post['region']->len()<3) throw new Exception(_e::get('EDIT_ACCOUNT_REGION'));
                $me->region = (string)_::$post['region'];
            }
            if(_::$post['sexo']->int() < 1 || _::$post['sexo']->int() > 2) throw new Exception(_e::get('EDIT_ACCOUNT_SEXO'));
            $me->sexo = _::$post['sexo']->int();
            
            $fecha_dia = _::$post['dia']->int();
            $fecha_mes = _::$post['mes']->int();
            $fecha_anio = _::$post['anio']->int();
            if($fecha_dia<1 || $fecha_dia>31) throw new Exception(_e::get('EDIT_ACCOUNT_FECHA_NAC'));
            $me->dia_n = $fecha_dia;
            if($fecha_mes<1 || $fecha_mes>12) throw new Exception(_e::get('EDIT_ACCOUNT_FECHA_NAC'));
            $me->mes_n = $fecha_mes;
            if($fecha_anio<1900 || $fecha_mes>2014) throw new Exception(_e::get('EDIT_ACCOUNT_FECHA_NAC'));
            $me->anio_n = $fecha_anio;
            
            $me->save();
            _::$view->assign('saved', true);
        } catch(Exception $e) {
            _::$view->assign('error', true);
            _::$view->assign('errorObject', json_decode($e->getMessage()));
        }
    }
    
    _::$view->show('otros/account_menu');
    _::$view->show('account');
});

_::define_controller('account_password', function(){
    _::$view->assign('section', null);
    _::$view->assign('subsection', 'account_password');
    _::$view->assign('error', false);
    _::$view->assign('errorObject', null);
    _::$view->assign('saved', false);
    if(_::$isPost)
    {
        $pass = (string)_::$post['old_pass'];
        $nueva[0] = (string)_::$post['new_pass'];
        $nueva[1] = (string)_::$post['new_pass2'];
        _e::set('NEWPASS_VIEJA_INVALIDA', 'La contraseña actual no es correcta');
        _e::set('NEWPASS_NUEVA_INVALIDA', 'La contraseña nueva debe tener al menos 8 caracteres');
        _e::set('NEWPASS_NUEVA_COINCIDENCIA', 'Las contraseñas no coinciden');
        try{
            if(!_::$post['old_pass']->check(_::$globals['me']->password)) throw new Exception(_e::get('NEWPASS_VIEJA_INVALIDA'));
            if(_::$post['new_pass']->len() < 8) throw new Exception(_e::get('NEWPASS_NUEVA_INVALIDA'));
            if($nueva[0] !== $nueva[1]) throw new Exception(_e::get('NEWPASS_NUEVA_COINCIDENCIA'));
            // realizar el cambio
            _::$globals['me']->password = (string)_::$post['new_pass']->hash();
            _::$globals['me']->save();
            _::$view->assign('saved', true);
        } catch(Exception $e) {
            _::$view->assign('error', true);
            // tirar error y ponerlo en el cartel correspondiente
            _::$view->assign('errorObject', json_decode($e->getMessage()));
        }
    }
    _::$view->show('otros/account_menu');
    _::$view->show('account_password');
});

_::define_controller('account_avatar', function(){
    // por el momento es lo único que hay
    $tipo = isset(_::$post['type']) ? _::$post['type']->int() : -1;
    
    try{
        _e::set('AVATAR_ERROR_UNABLED', 'Este metodo de subida está deshabilitado.');
        _e::set('AVATAR_ERROR_UPLOAD', 'El archivo no es valido.');
        _e::set('AVATAR_INVALID_LINK', 'El link proporcionado no es valido.');
        _e::set('AVATAR_IMAGE_TYPE', 'Solo se permiten imagenes GIF, JPG/JPEG, PNG');
        _e::set('AVATAR_MAX_WIDTH', 'La imagen es demasiado ancha (maximo '.MAX_IMAGE_WIDTH.'px)');
        _e::set('AVATAR_MAX_HEIGHT', 'La imagen es demasiado alta (maximo '.MAX_IMAGE_HEIGHT.'px)');
        _e::set('AVATAR_MAX_SIZE', 'La imagen pesa demasiado, (maximo '.MAX_IMAGE_SIZE.'kb)');
        $linkAvatar = false;
        if($tipo == 1) // por link
        {
            if(!AVATAR_EXTERNAL_LINK) throw new Exception(_e::get('AVATAR_ERROR_UNABLED'));
            if(!_::$post['link']->isLink()) throw new Exception(_e::get('AVATAR_INVALID_LINK'));
            $isize = getimagesize((string)_::$post['link']);
            if($isize[2] !== IMAGETYPE_GIF && $isize[2] !== IMAGETYPE_JPEG && $isize[2] !== IMAGETYPE_PNG)
                throw new Exception(_e::get('AVATAR_IMAGE_TYPE'));
            if(MAX_IMAGE_WH)
            {
                if($isize[0]>MAX_IMAGE_WIDTH) throw new Exception(_e::get('AVATAR_MAX_WIDTH'));
                if($isize[1]>MAX_IMAGE_HEIGHT) throw new Exception(_e::get('AVATAR_MAX_HEIGHT'));
            }
            if(MAX_IMAGE_SIZE!==false)
            {
                if(link_file_size((string)_::$post['link'])/1024 > MAX_IMAGE_SIZE) throw new Exception(_e::get('AVATAR_MAX_SIZE'));
            }
            // hosteamos los links?
            if(AVATAR_HOSTING_EXTERNAL)
            {
                $extension = '.cab';
                $picture = file_get_contents((string)_::$post['link']);
                if($isize[2] === IMAGETYPE_GIF)
                    $extension = '.gif';
                if($isize[2] === IMAGETYPE_JPEG)
                    $extension = '.jpg';
                if($isize[2] === IMAGETYPE_PNG)
                    $extension = '.png';
                
                if(!AVATAR_CDN)
                    unlink(__dir__.'/../'._::$globals['me']->avatar);
                else
                    unlink(__dir__.'/../'.str_replace(AVATAR_CDN_LINK, null, _::$globals['me']->avatar));
                file_put_contents('uploads/avatars/'._::$globals['me']->id_usuario.$extension, $picture);
                
                if(AVATAR_SCALE)
                {
                    if($isize[2] === IMAGETYPE_PNG)
                        $rsr_org = imagecreatefrompng('uploads/avatars/'._::$globals['me']->id_usuario.$extension);
                    elseif($isize[2] === IMAGETYPE_JPEG)
                        $rsr_org = imagecreatefromjpeg('uploads/avatars/'._::$globals['me']->id_usuario.$extension);
                    elseif($isize[2] === IMAGETYPE_GIF)
                        $rsr_org = imagecreatefromgif('uploads/avatars/'._::$globals['me']->id_usuario.$extension);
                    $rsr_scl = imagescale($rsr_org, AVATAR_SCALE_WIDTH, AVATAR_SCALE_HEIGHT,  IMG_BICUBIC_FIXED);
                    imagedestroy($rsr_org);
                    if($isize[2] === IMAGETYPE_JPEG)
                        imagejpeg($rsr_scl, 'uploads/avatars/'._::$globals['me']->id_usuario.$extension);
                    elseif($isize[2] === IMAGETYPE_PNG)
                        imagepng($rsr_scl, 'uploads/avatars/'._::$globals['me']->id_usuario.$extension);
                    elseif($isize[2] === IMAGETYPE_GIF)
                        imagegif($rsr_scl, 'uploads/avatars/'._::$globals['me']->id_usuario.$extension);
                    imagedestroy($rsr_scl);
                }
                
                if(!AVATAR_CDN)
                    $linkAvatar = '/uploads/avatars/'._::$globals['me']->id_usuario.$extension;
                else
                    $linkAvatar = AVATAR_CDN_LINK._::$globals['me']->id_usuario.$extension;
                    
            } else $linkAvatar = (string)_::$post['link'];
        } elseif($tipo == 2) { // uploader de imagenes

            if(!AVATAR_UPLOADER) throw new Exception(_e::get('AVATAR_ERROR_UNABLED'));
            _::declare_component('file');
            $avatar = new file();
            
            $r = $avatar->uploadPhoto('avatar', __dir__.'/../uploads/avatars/', true, false, $extension_valida = array('jpg', 'jpeg', 'png', 'gif'));
            if($r === false) { throw new Exception(_e::get('AVATAR_ERROR_UPLOAD')); }
            
            // escalar
            if(AVATAR_SCALE)
            {
                $isize = getimagesize($r);
                if($isize[2] === IMAGETYPE_PNG)
                    $rsr_org = imagecreatefrompng($r);
                elseif($isize[2] === IMAGETYPE_JPEG)
                    $rsr_org = imagecreatefromjpeg($r);
                elseif($isize[2] === IMAGETYPE_GIF)
                    $rsr_org = imagecreatefromgif($r);
                $rsr_scl = imagescale($rsr_org, AVATAR_SCALE_WIDTH, AVATAR_SCALE_HEIGHT,  IMG_BICUBIC_FIXED);
                imagedestroy($rsr_org);
                if($isize[2] === IMAGETYPE_JPEG)
                    imagejpeg($rsr_scl, $r);
                elseif($isize[2] === IMAGETYPE_PNG)
                    imagepng($rsr_scl, $r);
                elseif($isize[2] === IMAGETYPE_GIF)
                    imagegif($rsr_scl, $r);
                imagedestroy($rsr_scl);
            }
            
            $r = str_replace(__dir__.'/..', null, $r);
            if(!AVATAR_CDN)
                $linkAvatar = $r;
            else
                $linkAvatar = AVATAR_CDN_LINK.$r;
            
        } elseif($tipo == 3) { // capturar con la camara
            
        }
        
        if($linkAvatar !== false)
        {
            _::$globals['me']->avatar = $linkAvatar;
            _::$globals['me']->save();
        }
        
        _::$view->ajax(array('error' => false));
    } catch (Exception $e)
    {
        _::$view->ajax_plain($e->getMessage());
    }
});

_::define_controller('account_profile', function(){
    _::$view->assign('section', null);
    _::$view->assign('error', false);
    _::$view->assign('saved', false);
    _::$view->assign('subsection', 'profile');
    
    if(perfiles::exists(_::$globals['me']->id_usuario))
    {
        $perfil = new perfiles(_::$globals['me']->id_usuario);
    } else {
        $perfil = new perfiles();
        $perfil->id_usuario = _::$globals['me']->id_usuario;
    }
    
    if(_::$isPost)
    {
        if(_::$post['facebook']->isLink())
            $perfil->facebook = (string)_::$post['facebook'];
        else
            $perfil->facebook = 'No especificado';
        if(_::$post['twitter']->isLink())
            $perfil->twitter = (string)_::$post['twitter'];
        else
            $perfil->twitter = 'No especificado';
        if(_::$post['steam']->isLink())
            $perfil->steam = (string)_::$post['steam'];
        else
            $perfil->steam = 'No especificado';
        if(_::$post['battlenet']->len() > 3)
            $perfil->battlenet = (string)_::$post['battlenet'];
        else
            $perfil->battlenet = 'No especificado';
        if(_::$post['xbox']->len() > 3)
            $perfil->xbox = (string)_::$post['xbox'];
        else
            $perfil->xbox = 'No especificado';
            
        // segundo grupo
        if(_::$post['intereses']->len() > 3)
            $perfil->intereses = (string)_::$post['intereses'];
        else
            $perfil->intereses = 'No especificado';
        if(_::$post['hobbies']->len() > 3)
            $perfil->hobbies = (string)_::$post['hobbies'];
        else
            $perfil->hobbies = 'No especificado';
        if(_::$post['series']->len() > 3)
            $perfil->series = (string)_::$post['series'];
        else
            $perfil->series = 'No especificado';
        if(_::$post['musica']->len() > 3)
            $perfil->musica = (string)_::$post['musica'];
        else
            $perfil->musica = 'No especificado';
        if(_::$post['deportes']->len() > 3)
            $perfil->deportes = (string)_::$post['deportes'];
        else
            $perfil->deportes = 'No especificado';
        if(_::$post['libros']->len() > 3)
            $perfil->libros = (string)_::$post['libros'];
        else
            $perfil->libros = 'No especificado';
        if(_::$post['peliculas']->len() > 3)
            $perfil->peliculas = (string)_::$post['peliculas'];
        else
            $perfil->peliculas = 'No especificado';
        if(_::$post['comidas']->len() > 3)
            $perfil->comidas = (string)_::$post['comidas'];
        else
            $perfil->comidas = 'No especificado';
        if(_::$post['heroes']->len() > 3)
            $perfil->heroes = (string)_::$post['heroes'];
        else
            $perfil->heroes = 'No especificado';
            
        $perfil->save();
    }
    
    _::$view->assign('profile', $perfil);
    _::$view->show('otros/account_menu');
    _::$view->show('account_profile');
});

_::define_controller('account_options', function(){
    _::redirect('/account',false);
});

_::define_controller('account_bans', function(){
    _::redirect('/account',false);
});

_::define_controller('account_design', function(){

    _::$view->assign('section', null);
    _::$view->assign('subsection', 'design');
    
    if(_::$isPost)
    {
        // guardar
        if((string)_::$post['theme'] == 'v4')
            _::$globals['me']->theme = 1;
        else if((string)_::$post['theme'] == 'v9')
            _::$globals['me']->theme = 2;
        _::$globals['me']->save();
        $_SESSION['themeSelected'] = (string)_::$post['theme'];
        _::redirect('/account_design', false);
        die();
    }
    
    _::$view->show('otros/account_menu');
    _::$view->show('account_design');
    
});