<?php
namespace Mega\Control;
use \Mega\Control as control;
use \Mega\Cls\core as core;
class radioitem extends control\radioitem\module{

	private $config;
		
	function __construct($id = ''){
		$this->config = [];
		parent::__construct();
		$this->config['NAME'] = core\general::randomString(20);
		$this->config['ID'] = 'radiobutton';
		if($id != ''){
			$this->config['ID'] = $id;
		}
		$this->config['FORM'] = 'FORM';
		$this->config['STYLE'] = '';
		$this->config['CLASS'] = '';
		$this->config['CSS_FILE'] = '';
		$this->config['VALUE'] = 'radiobutton';
		$this->config['SIZE'] = 12;
		$this->config['LABEL'] = 'Form Label';
		$this->config['CHECKED'] = FALSE;
		$this->config['DISABLED'] = FALSE;
		$this->config['LABEL'] = 'radiobutton';
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
		if(key_exists($key, $this->config)){		
			$this->config[$key] = $value;
			return TRUE;
		}
		return FALSE;
	}
	
}
