<?php class_exists('_') || die('FORBIDDEN');

// this function is deprecated since version 1.0.1, changed by _::define_autocall();
_::define_autocall(function(){
        _::$view->assign('glberror', false);
        
        // autologin cookie
        if(!isset(_::$session['me']) && isset($_COOKIE['autoLoginUser']) && isset($_COOKIE['autoLoginKey']))
        {
                $uid = new cookieVar('autoLoginUser');
                $ukey = new cookieVar('autoLoginKey');
                $user = new usuarios($uid->int());
                if($user->token_activacion == (string)$ukey)
                {
                        $user->last_ip = get_real_ip();
                        $user->save();
                        if($user->theme < 2)
                        {
                            $_SESSION['themeSelected'] = 'v4';
                        } else {
                            $_SESSION['themeSelected'] = 'v9';
                        }
                        $sess = new sessionVar('me');
                        $sess->set($user->id_usuario);
                        _::$session['me'] = $user->id_usuario;
                } else {
                        $user->token_activacion = md5('RELogin-TOKEN'.base64_encode(mt_rand(10000, 999999).'RELogin-TOKEN').mt_rand(10000, 999999));
                        $user->save();
                        security::fatalidad('Login', 'Token invalido / Cookie Hack? ip:'.get_real_ip(), $user->id_usuario);
                }
        }
        
        if(isset(_::$session['me']))
        {
                _::$view->assign('login', true);
                _::$globals['me'] = new usuarios((string)_::$session['me']);
                _::$view->assign('me', _::$globals['me']);
                _::$view->assign('GTPcantNotify', notificaciones::countNews(_::$globals['me']->id_usuario));
                _::$view->assign('GTPcantMPS', mensajes_recibidos::getNewsMPS(_::$globals['me']->id_usuario));
                // if is banned
                if(_::$globals['me']->status_user == 2 && (string)_::$get['action'] != 'banned')
                {
                        _::redirect('/banned', false);
                        die();
                }
                $proxy = false;
                $ip = getIpAndProxy($proxy);
                // if is proxy
                _::$view->assign('GTPRealIP', $ip);

                if($proxy && BLOCK_ALL_PROXY)
                {
                        _::redirect('/proxy', false);
                        die();
                }
                // if is ip banned
                $ban = new iptables($ip);
                if(!$ban->void)
                {
                        header('HTTP/1.0 403 Forbidden');
                        $html = '<h1>403 Forbidden</h1> You don\'t have permission to access nothing on this server.<hr /> <i>Script-IpTables ip banned at '.date('d/m/Y').'</i>';
                        die($html);
                }
        } else {
                _::$view->assign('login', false);
                _::$view->assign('needCaptcha', isset($_SESSION['needCaptcha']));
        }
        
        _::$globals['categorias'] = _::factory(categorias::getAll('ORDER BY nombre_categoria DESC'), 'id_categoria', 'categorias');
        _::$view->assign('categorias', _::$globals['categorias']);
        
        if(!isset(_::$session['me']))
        {
                _::$globals['paises'] = _::factory(paises::getAll(), 'id_pais', 'paises');
        }
        _::$view->show('header');
    });