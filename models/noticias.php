<?php class_exists('_') || die('FORBIDDEN');

// this represent structure of table example
class noticias extends table
{
    // the primary keys (use array to multiple keys)
    protected $primaryKeys = 'id_noticia'; // one or multiple keys
    
    public $id_noticia;
    public $contenido;

}