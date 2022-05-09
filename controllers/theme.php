<?php

_::define_controller('theme', function(){
    if(_::$get['page']->int() == 1)
    {
        $_SESSION['themeSelected'] = 'v4';
    } else {
        $_SESSION['themeSelected'] = 'v9';
    }
    _::redirect('/', false);
    die();
});