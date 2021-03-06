<?php if(php_sapi_name() !== 'cli') die('PHPQuery Helper only work in CLI mode.');

require(__dir__.'/base.php');

console::write('====== MODEL MAKER FOR PHPQUERY ====');
console::write('= PHPQuery DATA:');
$folder = console::read('Model Folder (default: /models):>');
console::write('============');
console::write('= DB DATA:');
$host = console::read('Host (default: localhost):>');
$user = console::read('User (default: root):>');
$pass = console::read('Pass (default: null):>');
$dbname = console::read('DB Name (default: test):>');

$folder = empty($folder) ? '/models' : $folder;
$host = empty($host) ? 'localhost' : $host;
$user = empty($user) ? 'root' : $user;
$pass = empty($pass) ? '' : $pass;
$dbname = empty($dbname) ? 'test' : $dbname;

DB::init($host, $user, $pass, $dbname);

$model_location = __dir__.'/../..'.$folder.'/';

$models = getTables();

foreach($models as $newModel)
{
    $file = $newModel.'.php';
    if(!file_exists($model_location.$file))
    {
        console::write('[+] Model '.$newModel.': ', false);
        $data = getTableFields($newModel);
        
        $fields = $data['fields'];
        $pks = $data['pk'];
        
        $modelBody = '<?php'.PHP_EOL.PHP_EOL;
        $modelBody .= '// model autogenerated by model_maker.php'.PHP_EOL;
        $modelBody .= 'class '.$newModel.' extends table'.PHP_EOL;
        $modelBody .= '{'.PHP_EOL;
        $modelBody .= '    // Primary keys'.PHP_EOL;
        console::write('[pks: '.count($pks).'] - ', false);
        if(count($pks) == 1)
            $modelBody .= '    protected $primaryKeys = \''.$pks[0].'\';'.PHP_EOL.PHP_EOL;
        else
        {
            $pks = implode('\', \'', $pks);
            $modelBody .= '    protected $primaryKeys = array(\''.$pks.'\');'.PHP_EOL.PHP_EOL;
        }
        $modelBody .= '    // fields:'.PHP_EOL;
        foreach($fields as $uniqueField)
            $modelBody .= '    public $'.$uniqueField.' = null;'.PHP_EOL;
        $modelBody .= PHP_EOL.'}';
        console::write('[fields: '.count($fields).']');
        
        if(count($pks) == 0)
            console::write('[!!!] Warning, table '.$newModel.' without Primary Key');
        
        file_put_contents($model_location.$file, $modelBody);
    } else console::write('[!] File '.$file.' already exists.');
}

//var_dump(getTableFields('support_responses'));

function getTableFields($table)
{
    $out = array();
    $pks = array();
    foreach(DB::fetchAll(DB::query('describe '.$table)) as $field)
    {
        $out[] = $field['Field'];
        if($field['Key'] == 'PRI')
            $pks[] = $field['Field'];
        
    }
    return array('fields' => $out, 'pk' => $pks);
}

function getTables()
{
    $tables = array();
    foreach(DB::fetchAll(DB::query('SHOW TABLES')) as $table)
    {
        $tables[] = $table[0];
    }
    return $tables;
}