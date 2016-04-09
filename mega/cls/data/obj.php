<?php
namespace Mega\Cls\Data;
/**
 * @author babak alizadeh
 * @copyright 2014 gnu gpl v3
 * this function is base of objects that working with data like string ,integer, boolean and ...
 * 
 */

class obj{
	
    public $values;
    
    public function __construct(){
		$this->values = [];
	}
	
	/*
	 * function for set
	 * @param string $key
	 * @param string $value
	 */
	function _set($key,$value){
		$this->values[$key] = $value;
	}
	
	/*
	 * function for get
	 * @param string $key
	 * @param string $value
	 */
	function _get($key){
		if(array_key_exists($key,$this->values))
			return $this->values[$key];
	}
	
}
?>
