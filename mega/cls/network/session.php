<?php
namespace Mega\Cls\network;
//for use this class we should start sessions			

class session {
	
	public function __construct(){
	
	}
	
	public static function set($key,$value){
		$_SESSION[$key]=$value;
	}
	
	public static function get($key){
		#return 0 mean not found
		if (!isset($_SESSION[$key])){ return null;}
		return $_SESSION[$key];
	}
	
	public static function is_set($key){
		if (isset($_SESSION[$key])){ return TRUE;}
		return false;
	}
	
	public static function delete($key){
		#session key not found
		if (!isset($_SESSION[$key])){ return TRUE;}
		unset($_SESSION[$key]);
		return TRUE;	
	}

}

?>
