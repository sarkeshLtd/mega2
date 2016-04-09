<?php
namespace Mega\Control;
use \Mega\Control as control;
class tile extends control\tile\module{
	
	private $items;
	private $config;
	private $places;
	
	function __construct(){
		parent::__construct();
		$this->items = array();
		$this->places = array();
		
		//this config is for set template and template directore for showing
		$this->config['TEMPLATE'] = '';
		$this->config['TEMPLATE_DIR'] = '';
		
		//this config use for attech javascript(js) file to header of page
		$this->config['SCRIPT_SRC'] = '';
		
		//THIS config set css file for paste to headet of page
		$this->config['CSS_FILE'] = '';
	}
	
	public function draw(){
		return $this->module_draw($this->items, $this->config,$this->places);
	}
	
	public function add($item,$place=''){
		if($place != ''){
			array_push($this->places, $place);
		}
		if(is_string($item))
			array_push($this->items,$item);
		else
			array_push($this->items,$item->draw());
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
	
	//this function add space bettween tile parts
	public function add_spc(){
		$this->add('<br>');
		
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
