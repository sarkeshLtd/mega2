<?php
namespace Mega\Control;
use \Mega\Control as control;
class textarea extends control\textarea\module{
	private $config;
	
	function __construct($name=''){
		
		parent::__construct();
		$this->config = [];
		$this->config['NAME'] = 'TEXTAREA';
		if($name != ''){
			$this->config['NAME'] = $name;
		}
		$this->config['LABEL'] = '';
		$this->config['HELP'] = '';
		$this->config['EDITOR'] = true;
        //set editor name  nicedit or summernote
        $this->config['DEFAULT_EDITOR'] = 'nicedit';

		$this->config['ROWS'] = '10';
		$this->config['SIZE'] = '12';
		$this->config['STYLE'] = '';
		$this->config['FORM'] = 'DEFAULT_FORM_NAME';
		$this->config['CLASS'] = '';
		$this->config['CSS_FILE'] = '';
		$this->config['VALUE'] = '';
	}
	
	public function draw(){
		return $this->module_draw($this->config);
	}
	
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
		die('Index is out of range');
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
