<?php

// dbData sim
class dbData
{
    static public $host = 'localhost';
    static public $user = 'root';
    static public $pass = null;
    static public $db = null;
}

// coreData-sim
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

// kernel-sim
class _
{
    static public $globals = array();
}