<?php
namespace Mega\Control;
use \Mega\Control as control;
class wall extends control\wall\module{
	
	private $config;
	function __construct($value=""){
		
		$this->config['VALUE'] = $value;
		$this->config['CLASS'] = '';
		$this->config['STYLE'] = '';
		//TYPE string (success,info,warning,danger)
		$this->config['TYPE'] = 'warning';
		$this->config['BS_CONTROL'] = TRUE;
		parent::__construct();
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
