<?php

if(!defined('PHPQUERY_LOADER')) {
	include('../index.html');
	die();
}

if(!file_exists('settings.php'))
{
	return null;
} else {
	$dsn = 'mysql:dbname='.dbData::$db.';host='.dbData::$host;
	try{
		return new PDO($dsn, dbData::$user, dbData::$pass);
	} catch(PDOException $e)
	{
		_::onDbFail();
		return null;
	}
	
}