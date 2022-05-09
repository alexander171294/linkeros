<?php class_exists('_') || die('FORBIDDEN');

// this represent structure of table example
class iptables extends table
{
    protected $primaryKeys = 'ip'; // one or multiple keys
    
    public $ip;
    public $fecha;
    public $moderator;
    
    public $userObject = null;
    
    public function execIpTables()
    {
        // execute command to ban with iptables
    }
    
    public function getIp()
    {
        return long2ip($this->ip);
    }
    
    public function getMod()
    {
        if(empty($this->userObject))
            $this->userObject = new usuarios($this->moderator);
        return $this->userObject;
    }
    
    public function getFecha()
    {
        $fecha = new _date($this->fecha);
        return $fecha->fHour(':')->fMinute(' ')->fDay('/')->fMonth('/')->fYear()->format();
    }
    
}