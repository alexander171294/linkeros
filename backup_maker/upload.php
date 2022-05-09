#!/usr/bin/env php
<?php

ini_set('display_errors', true);

require_once __DIR__.'/helper.php';
use \Dropbox as dbx;

// hacemos el backup
require_once __DIR__.'/phpq_compat.php';
require_once __DIR__.'/../settings.php';
$dbhost = dbData::$host;
$dbuser = dbData::$user;
$dbpass = dbData::$pass;
$dbname = dbData::$db;
$backupFile = 'backup_'.date('d').'-'.date('m').'-'.date('Y').'_'.time().'.sql';
$command = "mysqldump --opt -h $dbhost --user='$dbuser' --password='$dbpass' $dbname | gzip > $backupFile";
system($command);

// subimos el backup

list($client) = parseArgs("upload-file", $argv);

$sourcePath = $backupFile;
$dropboxPath = '/'.$backupFile;

$pathError = dbx\Path::findErrorNonRoot($dropboxPath);
if ($pathError !== null) {
    fwrite(STDERR, "Invalid <dropbox-path>: $pathError\n");
    die;
}

$size = null;
if (\stream_is_local($sourcePath)) {
    $size = \filesize($sourcePath);
}

$fp = fopen($sourcePath, "rb");
$metadata = $client->uploadFile($dropboxPath, dbx\WriteMode::add(), $fp, $size);
fclose($fp);

print_r($metadata);
