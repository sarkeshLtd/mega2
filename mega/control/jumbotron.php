<?php
namespace Mega\Control;
use \Mega\Control as control;
class jumbotron extends control\jumbotron\module{
	
	private $config;
	function __construct(){
		
		$this->config['TITLE'] = '';
		$this->config['BODY'] = '';
        //TYPE string (success,info,warning,danger)
        $this->config['BTN_TYPE'] = 'success';
        $this->config['BTN_LABEL'] = '';
        $this->config['BTN_URL'] = '#';

	}
	public function draw(){
		return $this->module_draw($this->config);
	}
	public function get($key){
		if(key_exists($key, $this->config)){
			return $this->config[$key];
		}
		return false;
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
?>
