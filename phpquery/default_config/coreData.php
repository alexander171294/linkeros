<?php

if(!defined('PHPQUERY_LOADER')) {
	include('../index.html');
	die();
}

class coreData
{
	
	static public $m = 'models/';
	static public $v = 'views/';
	static public $c = 'controllers/';
	static public $extra = 'extras/';
	
	static public $component = '/components/';
	
	static public $default_404_controller = null;
	static public $default_500_controller = null;
}