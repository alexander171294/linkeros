<?php class_exists('_') || die('FORBIDDEN');

// this represent structure of table example
class mod_history extends table
{
    // the primary keys (use array to multiple keys)
    protected $primaryKeys = 'id_history'; // one or multiple keys
    
    public $id_history;
    public $tipo_target;
    public $id_target;
    public $accion;
    public $fecha;
    public $id_moderador;
    public $original_title = '';
    
    public $postObject = null;
    public $userObject = null;
    public $targetUserObject = null;
    
    public function getPost()
    {
        if(empty($this->postObject))
            $this->postObject = new post($this->id_target);
        return $this->postObject;
    }
    
    public function getUser()
    {
        if(empty($this->targetUserObject))
            $this->targetUserObject = new usuarios($this->id_target);
        return $this->targetUserObject;
    }
    
    public function getMod()
    {
        if(empty($this->userObject))
            $this->userObject = new usuarios($this->id_moderador);
        return $this->userObject;
    }
    
    public function getAction()
    {
        return (new moderacion_tipos_acciones($this->accion))->descripcion;
    }
    
    public function getFecha()
    {
        $fecha = new _date($this->fecha);
        return $fecha->fHour(':')->fMinute(' ')->fDay('/')->fMonth('/')->fYear()->format();
    }
    
}
