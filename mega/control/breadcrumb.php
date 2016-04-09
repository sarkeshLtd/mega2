<?php
namespace Mega\Control;
use \Mega\Control as control;
class breadcrumb extends control\breadcrumb\module{
    private $e;
    function __construct(){
        parent::__construct();
        $this->e = [];
    }

    public function draw(){
        return $this->module_draw($this->e);
    }

    //this function configure control//
    public function configure($key, $value){
        // checking for that key is exists//
        if(key_exists($key, $this->config)){
            $this->config[$key] = $value;
            return TRUE;
        }
        //key not exists//
        return FALSE;
    }

    public function add($url,$label){
        $obj = new \Mega\Cls\Data\obj();
        $obj->label = $label;
        $obj->url = $url;
        array_push($this->e,$obj);
    }

    /*
     * function use for set configs like object
     * @param strin $key, key of config
     * @param string $value, value of config
     * @return boolean result
     */
    public function __set($key,$value){
        $key = strtoupper($key);
        if(key_exists($key, $this->config)){
            $this->config[$key] = $value;
            return TRUE;
        }
        return FALSE;
    }
}
