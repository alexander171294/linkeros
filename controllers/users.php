<?php

_::define_controller('check_nick', function(){
    $nick = trim((string)_::$post['nick']);
    _e::set('REGISTRO_NICK_INVALID', 'El nick es invalido (a-z 0-9 - _)');
    _e::set('REGISTRO_NICK_SMALL', 'El nick es muy pequeño');
    _e::set('REGISTRO_NICK_EXIST', 'Ya existe un usuario con este nick');
    _e::set('REGISTRO_NICK_BIG', 'El nick es demasiado grande');
    _e::set('REGISTRO_CLOSE', 'No es posible registrarte en este momento.');
    try{
        $regex = '/^[a-zA-Z0-9-_]+$/';
        if(!SECCION_REGISTRO) throw new Exception(_e::get('REGISTRO_CLOSE'));
        if(!preg_match($regex, (string)_::$post['nick'])) throw new Exception(_e::get('REGISTRO_NICK_INVALID'));
        if(_::$post['nick']->len()<3) throw new Exception(_e::get('REGISTRO_NICK_SMALL'));
        if(usuarios::exists_nick($nick)) throw new Exception(_e::get('REGISTRO_NICK_EXIST'));
        if(_::$post['nick']->len()>32) throw new Exception(_e::get('REGISTRO_NICK_BIG'));
        _::$view->ajax(array('error' => false));
    } catch(Exception $e){
        _::$view->ajax_plain($e->getMessage());
    }
});

_::define_controller('check_email', function(){
    $email = trim((string)_::$post['email']);
    _e::set('REGISTRO_EMAIL_INVALID', 'El email no es válido');
    _e::set('REGISTRO_EMAIL_EXIST', 'Ya existe un usuario con este email');
    _e::set('REGISTRO_CLOSE', 'No es posible registrarte en este momento.');
    try{
        if(!SECCION_REGISTRO) throw new Exception(_e::get('REGISTRO_CLOSE'));
        if(!_::$post['email']->isEmail()) throw new Exception(_e::get('REGISTRO_EMAIL_INVALID'));
        if(usuarios::exists_email($email)) throw new Exception(_e::get('REGISTRO_EMAIL_EXIST'));
        _::$view->ajax(array('error' => false));
    } catch(Exception $e){
        _::$view->ajax_plain($e->getMessage());
    }
});

_::define_controller('realizar_registro', function(){
    $post = _::stringify(_::$post);
    _e::set('REGISTRO_NICK', 'El nick no es valido');
    _e::set('REGISTRO_PASS', 'Las pass no son correctas');
    _e::set('REGISTRO_EMAIL', 'El email no es registrable');
    _e::set('REGISTRO_TOS', 'Debes aceptar los terminos');
    _e::set('REGISTRO_CLOSE', 'Oops por el momento el registro se encuentra deshabilitado.');
    _e::set('REGISTRO_CAPTCHA', 'El captcha no es valido.');
    _e::set('REGISTRO_EMAILSEND', 'Ocurrió un error al procesar el envio de email.');
    _e::set('REGISTRO_NOINSERT', 'Ocurrió un error inesperado, por favor contacte un administrador.');
    try{
        if(!SECCION_REGISTRO) throw new Exception(_e::get('REGISTRO_CLOSE'));
        $regex = '/^[a-zA-Z0-9-_]+$/';
        if(preg_match($regex, (string)_::$post['nick']) === false) throw new Exception(_e::get('REGISTRO_NICK_INVALID'));
        if(_::$post['nick']->len() < 3 || _::$post['nick']->len() > 32 || usuarios::exists_nick((string)_::$post['nick'])) throw new Exception(_e::get('REGISTRO_NICK'));
        if($post['pass'] !== $post['pass2'] || _::$post['pass']->len() < 8) throw new Exception(_e::get('REGISTRO_PASS'));
        if(!_::$post['email']->isEmail() || usuarios::exists_email((string)_::$post['email'])) throw new Exception(_e::get('REGISTRO_EMAIL'));
        if($post['tos'] !== 'true') throw new Exception(_e::get('REGISTRO_TOS'));
        if(REQUIRE_REGISTRO_CAPTCHA)
        {
            _::declare_component('curl');
            $r = curlPost('https://www.google.com/recaptcha/api/siteverify', array('secret' => RECAPTCHA_SECRET, 'response' => $post['captcha']));
            if(json_decode($r)->success !== true) throw new Exception(_e::get('REGISTRO_CAPTCHA'));
        }
        $usuario = new usuarios();
        $usuario->nick = strtolower($post['nick']);
        $usuario->nombre = ' ';
        $usuario->email = $post['email'];
        $usuario->id_rango = 1; // novato
        $usuario->sexo = _::$post['genero']->int();
        $usuario->dia_n = _::$post['dia']->int();
        $usuario->mes_n = _::$post['mes']->int();
        $usuario->anio_n = _::$post['anio']->int();
        $usuario->pais = _::$post['pais']->int();
        $usuario->region = $post['region'];
        $usuario->status_user = SECCION_REGISTRO_EMAILS ? 0 : 1; // 0 pendiente de email.
        $usuario->token_activacion = md5(base64_encode(mt_rand(10000, 999999).'TOKEN'));
        $usuario->puntos_obtenidos = 0;
        $usuario->puntos_disponibles = 0;
        $usuario->post_creados = 0;
        $usuario->comentarios_creados = 0;
        $usuario->seguidores = 0;
        $usuario->siguiendo = 0;
        $usuario->mensaje_perfil = ' ';
        $usuario->last_activity_post = time();
        $usuario->last_activity_comment = time();
        $usuario->last_activity_mp = time();
        $usuario->password = (string)_::$post['pass']->hash();
        $usuario->avatar = DEFAULT_AVATAR_LINK;
        $usuario->nfu_desde = 0;
        $usuario->fecha_registro = time();
        // damos de alta el usuario
        $id = $usuario->save();
        if($id == 0) throw new Exception(_e::get('REGISTRO_NOINSERT'));
        // aquí enviaríamos el mail de activación
        $body = 'Bienvenido!, y muchas gracias por registrarte.<br />';
        $body .= 'Tus datos:<br />';
        $body .= 'Usuario: <b>'.$usuario->nick.'</b><br />';
        $body .= 'Contrase&ntilde;a: <b>'.(string)$post['pass2'].'</b><br /><hr>';
        if(SECCION_REGISTRO_EMAILS)
        {
            $body .= 'Debes seguir el <a href="'._::$globals['WEB_LINK'].'/?action=check_account&usrID='.$id.'&token='.urlencode($usuario->token_activacion).'">siguiente enlace</a> para activar tu cuenta ('._::$globals['WEB_LINK'].'/?action=check_account&usrID='.$id.'&token='.urlencode($usuario->token_activacion).')<br />';
        } else {
            $body .= 'Tu cuenta ya se encuentra activa y puedes utilizarla<br />';
        }
        $body .= '<br />Atte. STAFF de '._::$globals['WEB_SITE'];
        
        if(!REGISTRO_SMTP)
        {
            _::declare_component('mailer');
            $mail = new mailer(EMAIL_WEBMASTER, $usuario->email, 'Bienvenido a '._::$globals['WEB_SITE'], $body);
            if($mail->send())
            {
                _::$view->ajax(array('error' => false));
            } else throw new Exception(_e::get('REGISTRO_EMAILSEND'));
        } else {
            _::declare_component('phpmailer');
            $mail = new PHPMailer();
            //$mail->IsSMTP();
            $mail->SMTPDebug = 2;
            $mail->SMTPAuth = true;
            $mail->IsHTML(true);
            if(REGISTRO_SMTP_SSL)
            {
                $mail->SMTPSecure = 'ssl';
                $mail->Host = REGISTRO_SMTP_SERVER;
                $mail->Port = 465;
            }else {
                $mail->SMTPSecure = 'tls';
                $mail->Host = REGISTRO_SMTP_SERVER;
                $mail->Port = 587;
            }
            $mail->Username = EMAIL_WEBMASTER;
            $mail->Password = EMAIL_WEBMASTER_PASSWORD;
            $mail->SetFrom(EMAIL_WEBMASTER, _::$globals['WEB_SITE']);
            $mail->Subject = 'Bienvenido a '._::$globals['WEB_SITE'];
            $mail->MsgHTML($body);
            $address = $usuario->email;
            $mail->AddAddress($address, $usuario->nick);
            if($mail->Send())
            {
                _::$view->ajax(array('error' => false));
            } else throw new Exception(_e::get('REGISTRO_EMAILSEND'));
        }
        
    } catch(Exception $e){
        _::$view->ajax_plain($e->getMessage());
    }
    
});

_::define_controller('check_account', function(){
    $uid = _::$get['usrID']->int();
    _::$view->assign('section', null);
    _::$view->assign('subsection', null);
    $token = (string)_::$get['token'];
    $user = new usuarios($uid);
    if($user->void) die('VOID_USR');
    if($user->status_user != 0) die('PREV_CHCK');
    if($user->token_activacion !== $token)
    {
        $user->token_activacion = md5(base64_encode(mt_rand(10000, 999999).'TOKEN'));
        _::declare_component('mailer');
        $body = 'A continuación te enviamos un nuevo token de activación<br /><hr>';
        $body .= 'Debes seguir el <a href="'._::$globals['WEB_LINK'].'/?action=checkmail&usrID='.$uid.'&token='.urlencode($user->token_activacion).'">siguiente enlace</a> para activar tu cuenta<br />';
        $body .= '<br />Atte. STAFF de '._::$globals['WEB_SITE'];
        
        if(!REGISTRO_SMTP)
        {
            _::declare_component('mailer');
            $mail = new mailer(EMAIL_WEBMASTER, $usuario->email, 'Bienvenido a '._::$globals['WEB_SITE'], $body);
            if($mail->send())
            {
               _::$view->show('mailcheck_invalid');
            } else throw new Exception(_e::get('REGISTRO_EMAILSEND'));
        } else {
            _::declare_component('phpmailer');
            $mail = new PHPMailer();
            //$mail->IsSMTP();
            $mail->SMTPDebug = 2;
            $mail->SMTPAuth = true;
            $mail->IsHTML(true);
            if(REGISTRO_SMTP_SSL)
            {
                $mail->SMTPSecure = 'ssl';
                $mail->Host = REGISTRO_SMTP_SERVER;
                $mail->Port = 465;
            }else {
                $mail->SMTPSecure = 'tls';
                $mail->Host = REGISTRO_SMTP_SERVER;
                $mail->Port = 587;
            }
            $mail->Username = EMAIL_WEBMASTER;
            $mail->Password = EMAIL_WEBMASTER_PASSWORD;
            $mail->SetFrom(EMAIL_WEBMASTER, _::$globals['WEB_SITE']);
            $mail->Subject = 'Bienvenido a '._::$globals['WEB_SITE'];
            $mail->MsgHTML($body);
            $address = $user->email;
            $mail->AddAddress($address, $user->nick);
            if($mail->Send())
            {
                _::$view->show('mailcheck_invalid');
            } else throw new Exception(_e::get('REGISTRO_EMAILSEND'));
        }
            
    } else {
        $user->status_user = 1;
        $user->save();
        _::$view->show('mailcheck_valid');
    }
});

_::define_controller('ajax_login', function(){
    _e::set('LOGIN_NICK','El nick no existe');
    _e::set('LOGIN_PASS','Contraseña erronea');
    _e::set('LOGIN_EMAIL','Debe activar su email');
    _e::set('LOGIN_BAN','Usted se encuentra banneado');
    _e::set('LOGIN_CLOSE', 'El sistema de usuarios se encuentra deshabilitado por el momento.');
    _e::set('LOGIN_CAPTCHA','El captcha no es valido');
    _e::set('LOGIN_CAPTCHA2','Debe ingresar el captcha');
    if(isset(_::$session['me'])) return false;
    try{
        if(!SECCION_LOGIN) throw new Exception(_e::get('LOGIN_CLOSE'));
        if(!usuarios::exists_nick((string)_::$post['nick'])) throw new Exception(_e::get('LOGIN_NICK'));
        $uObject = new usuarios(usuarios::get_by_nick((string)_::$post['nick']));
        if($uObject->login_intentos > MAX_LOGIN_INTENT)
        {
                // COMPROBAR CAPTCHA
                $post = _::stringify(_::$post);
                _::declare_component('curl');
                if(isset($post['captcha']))
                {
                    $r = curlPost('https://www.google.com/recaptcha/api/siteverify', array('secret' => RECAPTCHA_SECRET, 'response' => $post['captcha']));
                    if(json_decode($r)->success !== true) {
                        throw new Exception(_e::get('LOGIN_CAPTCHA'));
                        $_SESSION['needCaptcha'] = true;
                    }
                } else {
                    throw new Exception(_e::get('LOGIN_CAPTCHA2'));
                    $_SESSION['needCaptcha'] = true;
                }
                
        }
            $uObject->login_intentos++;
        if(!_::$post['pass']->check($uObject->password)) {
            $uObject->save();
            if($uObject->login_intentos > MAX_LOGIN_INTENT) $_SESSION['needCaptcha'] = true;
            throw new Exception(_e::get('LOGIN_PASS'));
        }
        if($uObject->status_user == 0) throw new Exception(_e::get('LOGIN_EMAIL'));
        if($uObject->status_user == 2) throw new Exception(_e::get('LOGIN_BAN'));
        $uObject->last_ip = get_real_ip();
        $uObject->login_intentos = 0;
        $uObject->token_activacion = md5('Login-TOKEN'.base64_encode(mt_rand(10000, 999999).'Login-TOKEN').mt_rand(10000, 999999));
        $uObject->save();
        if($uObject->theme < 2)
        {
            $_SESSION['themeSelected'] = 'v4';
        } else {
            $_SESSION['themeSelected'] = 'v9';
        }
        unset($_SESSION['needCaptcha']);
        
        // remember system
        if(isset(_::$post['remember']) && _::$post['remember'] == true)
        {
            $life = new _date();
            $life->days(30); // 30 días
            $cookie = new cookieVar('autoLoginUser');
            $cookie->set($uObject->id_usuario, $life);
            $cookie = new cookieVar('autoLoginKey');
            $cookie->set($uObject->token_activacion, $life);
        }
        
        $user = new sessionVar('me');
        $user->set($uObject->id_usuario);

        _::$view->ajax(array('error' => false));
    } catch(Exception $e){
        _::$view->ajax_plain($e->getMessage());
    }
});

_::define_controller('logout', function(){
    if(!isset(_::$session['me'])) return false;
    $sesion = new sesiones(_::$session['me']->int());
    $sesion->delete();
    _::$session['me']->destroy();
    $cookie = new cookieVar('autoLoginUser');
    $cookie->destroy();
    $cookie = new cookieVar('autoLoginKey');
    $cookie->destroy();
    die();
});

_::define_controller('ver_perfil', function(){
     if(!SECCION_PERFIL) {
        _::redirect('closed');
        return false;
    }
    _::$view->assign('section', null);
    _::$view->assign('subsection', 'profile_view');
    _::$view->assign('reportes', _::factory(razones_reportes_usuarios::getAll(), 'id_razon_reporte', 'razones_reportes_usuarios'));
    $id = _::$get['id']->int();
    $usuario = new usuarios($id);
    if($usuario->void) {
        _::redirect('e404');
        return true;
    }
    $edad = (date('Y', time())-$usuario->anio_n)-1;
    if($usuario->mes_n < date('m', time()))
    {
        if(($usuario->mes_n = date('m', time())) && ($usuario->dia_n < date('d', time())))
            $edad++;
        elseif($usuario->mes_n != date('m', time()))
            $edad++;
    }
    // si soy admin y el otro usuario no
    if(isset(_::$globals['me']) && _::$globals['me']->id_rango == 2 && $usuario->id_rango != 2)
    {
        _::$view->assign('editRango', true);
        _::$view->assign('rangos', _::factory(rangos::getAll(), 'id_rango', 'rangos'));
    } else _::$view->assign('editRango', false);
    
    $pais = new paises($usuario->pais);
    _::$view->assign('pais', $pais);
    
    $fecha = new _date($usuario->fecha_registro);
    $fecha->fDay('/')->fMonth('/')->fYear();
    _::$view->assign('fecha', $fecha->format());
    
    _::$view->assign('rango', new rangos($usuario->id_rango));
    
    _::$view->assign('online', sesiones::isOnline($usuario->id_usuario));
    
    _::$view->assign('activity_hoy', $usuario->getActivityHoy());
    _::$view->assign('activity_ayer', $usuario->getActivityAyer());
    _::$view->assign('activity_antes', $usuario->getActivityAntes());
    
    if(perfiles::exists($usuario->id_usuario))
        $perfil = new perfiles($usuario->id_usuario);
    else
        $perfil = new perfiles();
        
    _::$view->assign('posts', post::getAllObjects('id_post', 'WHERE id_usuario = ? AND borrador = 0 AND revision = 0  ORDER BY id_post DESC LIMIT '.MAX_POST_IN_PROFILE, array($usuario->id_usuario)));
    
    _::$view->assign('miniSeguidores', _::factory(seguidores_usuarios::getAll('WHERE id_usuario = ? LIMIT '.MAX_BLOCK_FOLLOWERS_IN_PROFILE, array($usuario->id_usuario)), 'id_seguidor', 'usuarios'));
    _::$view->assign('miniSiguiendo', _::factory(seguidores_usuarios::getAll('WHERE id_seguidor = ? LIMIT '.MAX_BLOCK_FOLLOWERS_IN_PROFILE, array($usuario->id_usuario)), 'id_usuario', 'usuarios'));
    
    _::$view->assign('fullSeguidores', _::factory(seguidores_usuarios::getAll('WHERE id_usuario = ?', array($usuario->id_usuario)), 'id_seguidor', 'usuarios'));
    _::$view->assign('fullSiguiendo', _::factory(seguidores_usuarios::getAll('WHERE id_seguidor = ?', array($usuario->id_usuario)), 'id_usuario', 'usuarios'));
    
    _::$view->assign('profile', $perfil);
    _::$view->assign('edad', $edad);
    _::$view->assign('user', $usuario);
    
    $sesion = new sesiones($usuario->id_usuario);
    _::$view->assign('lastActivity', date('H:i d/m/Y',$sesion->last_activity));
    _::$view->assign('lastPost', date('H:i d/m/Y',$usuario->last_activity_post));
    _::$view->assign('lastComment', date('H:i d/m/Y',$usuario->last_activity_comment));
    
    _::$view->assign('medals', _::factory(user_medals::getAll('WHERE id_usuario = ?', array($usuario->id_usuario)), 'id_medalla', 'medals'));
    
    _::$view->show('profile');
});


_::define_controller('new_data', function(){
    $email = _::$post['email'];
    
    if($email->isEmail())
    {
        $user = new usuarios(usuarios::get_by_email((string)_::$post['email']));
        if(!$user->void)
        {
            $npass = substr(md5(mt_rand(1000,9999).'AUTOMATIC_PASSWORD_GENERATOR'.mt_rand(1000,9999)), 0, 8);
            $passO = new objectVar($npass);
            $user->password = (string)$passO->hash();
            $user->save();
            $body = 'Hola <b>'.$user->nick.'</b><br/>';
            $body .= 'Su nueva contrase&ntilde;a es '.$npass.'<br />';
            $body .= 'Atte. El equipo de '._::$globals['WEB_SITE'];
            if(!REGISTRO_SMTP)
            {
                _::declare_component('mailer');
                $mail = new mailer(EMAIL_WEBMASTER, $user->email, 'Nueva password '._::$globals['WEB_SITE'], $body);
                if($mail->send())
                {
                    _::$view->ajax(array('error' => false));
                } else _::$view->ajax(array('error' => true));
            } else {
                _::declare_component('phpmailer');
                $mail = new PHPMailer();
                //$mail->IsSMTP();
                $mail->SMTPDebug = 2;
                $mail->SMTPAuth = true;
                $mail->IsHTML(true);
                if(REGISTRO_SMTP_SSL)
                {
                    $mail->SMTPSecure = 'ssl';
                    $mail->Host = REGISTRO_SMTP_SERVER;
                    $mail->Port = 465;
                }else {
                    $mail->SMTPSecure = 'tls';
                    $mail->Host = REGISTRO_SMTP_SERVER;
                    $mail->Port = 587;
                }
                $mail->Username = EMAIL_WEBMASTER;
                $mail->Password = EMAIL_WEBMASTER_PASSWORD;
                $mail->SetFrom(EMAIL_WEBMASTER, _::$globals['WEB_SITE']);
                $mail->Subject = 'Nueva password '._::$globals['WEB_SITE'];
                $mail->MsgHTML($body);
                $address = $user->email;
                $mail->AddAddress($address, $user->nick);
                if($mail->Send())
                {
                    _::$view->ajax(array('error' => false));
                } else throw new Exception(_e::get('REGISTRO_EMAILSEND'));
            }
        } else _::$view->ajax(array('error' => true));
    } else _::$view->ajax(array('error' => true));
});

_::define_controller('report_users',function(){
    if(!SYSTEM_REPORTES) die();
    _e::set('REPORT_NO_LOGIN', 'Debes estar logueado para poder reportar este mp');
    _e::set('REPORT_POST_INEX', 'El mp que quieres reportar no existe');
    _e::set('REPORT_PREREPORT', 'Ya habías reportado este usuario anteriormente');
    _e::set('REPORT_RAZON', 'La razón no es válida');
    _e::set('REPORT_MSG', 'Debes aclarar el reporte');
    try {
        if(!isset(_::$globals['me'])) throw new Exception(_e::get('REPORT_NO_LOGIN'));
        $idu = _::$post['id_user']->int();
        $u = new usuarios($idu);
        if($u->void) throw new Exception(_e::get('REPORT_POST_INEX'));
        if(users_reportes::prevReported(_::$globals['me']->id_usuario, $idu)) throw new Exception(_e::get('REPORT_PREREPORT'));
        $razon = _::$post['razon']->int();
        $razon = new razones_reportes_usuarios($razon);
        if($razon->void) throw new Exception(_e::get('REPORT_RAZON'));
        if(_::$post['message']->len()<3) throw new Exception(_e::get('REPORT_MSG'));
        $report = new users_reportes();
        $report->id_profile = $idu;
        $report->id_usuario = _::$globals['me']->id_usuario;
        $report->id_razon_reporte = $razon->id_razon_reporte;
        $report->mensaje = (string) _::$post['message'];
        $report->fecha = time();
        $report->revisado = 0;
        $report->save();
        _::$view->ajax(array('error' => false));
    } catch(Exception $e)
    {
        _::$view->ajax_plain($e->getMessage());
    }
});