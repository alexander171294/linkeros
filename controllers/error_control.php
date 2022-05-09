<?php class_exists('_') || die('FORBIDDEN');

/* CONTROLLERS:
 * - post 404
 * - post 403
 * - post 501
 * - global 404
 * - global 403
 * - global 500 (internal)
 * Sección Posts
 */

// seleccionamos la sección
_::$view->assign('section', 'posts');
_::$view->assign('subsection', null);

// controladores de esta sección
_::define_controller('post_404', function(){
	_::$view->assign('glberror', true);
	_::$view->show('p404');
});
_::define_controller('post_403', function(){
	_::$view->assign('glberror', true);
	_::$view->show('p403');
});
_::define_controller('post_501', function(){
	_::$view->assign('glberror', true);
	_::$view->show('p501');
});
_::define_controller('e404', function(){
	_::$view->assign('glberror', true);
	_::$view->show('e404');
});
_::define_controller('e403', function(){
	_::$view->assign('glberror', true);
	_::$view->show('e403');
});
_::define_controller('e500', function(){
	_::$view->show('e500');
});