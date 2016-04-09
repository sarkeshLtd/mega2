<?php
namespace Mega\Control;
use \Mega\Control as control;
class table extends control\table\module{
	
	private $config;
	
	function __construct(){
		parent::__construct($form='');
		
		$this->config = [];
		$this->config['NAME'] = 'TABLE';
		if($form == ''){
			$this->config['FORM'] = $form;
		}
		else{
			$this->config['FORM'] = 'FORM';
		}
		
		// valid : NORMAL | SOURCE
		$this->config['TYPE'] = 'NORMAL';
		$this->config['ROWS'] = [];
		$this->config['HEADERS'] = [];
		$this->config['SIZE'] = 12;
		$this->config['BS_CONTROL'] = TRUE;
		$this->config['BORDER'] = FALSE;
		$this->config['HOVER'] = FALSE;
		$this->config['STRIPED'] = FALSE;
		$this->config['CSS_FILE'] = '';
		$this->config['CLASS'] = '';
		$this->config['HEADERS_WIDTH'] = (array) null;
		$this->config['ALIGN_CENTER'] = (array) null;
		
	}
	//this function designed for add rows
	public function add_row($row){
		$this->config['TYPE'] = 'NORMAL';
		$items = (array) null;
		foreach($row->controls as $control){
			array_push($items,$control['object']->draw());
		}
		array_push($this->config['ROWS'],$items);
		
	}
	public function add_source($source){
		$this->config['TYPE'] = 'SOURCE';
		$this->config['ROWS'] = array_merge($this->config['ROWS'],$source);
		
	}
	public function draw(){
		return $this->module_draw($this->config);
	}
	public function configure($key,$value){
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
