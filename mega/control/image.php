<?php
namespace Mega\Control;
use \Mega\Control as control;

class image extends control\image\module{
	
	private $config;
	function __construct(){
		parent::__construct();
		$this->config = [];
		$this->config['LABEL'] = '';
		$this->config['ALT'] = '';
		$this->config['SRC'] = '';
		$this->config['HREF'] = '';
		//valid types is => img-thumbnail , img-circle , img-rounded
		$this->config['TYPE'] = 'img-thumbnail';
		$this->config['BS_CONTROL'] = TRUE;
		$this->config['RESPONSIVE'] = FALSE;
		$this->config['STYLE'] = '';
		$this->config['CLASS'] = '';
		$this->config['SIZE'] =12;
		$this->config['BORDER'] = false;
        $this->config['INLINE'] = TRUE;
		
	}
	
	public function draw(){
		
		return $this->module_draw($this->config);
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

	public function get($key){
		if(key_exists($key, $this->config)){
			return $this->config[$key];
		}
		die('Index is out of range form');
	}
	
	/*
	 * function use for set configs like object
	 * @param strin $key, key of config
	 * @param string $value, value of config
	 * @return boolean result
	 */
	public function __set($key,$value){
		$key = strtoupper($key);
		if(array_key_exists($key, $this->config)){		
			$this->config[$key] = $value;
			return TRUE;
		}
		return FALSE;
	}
}
