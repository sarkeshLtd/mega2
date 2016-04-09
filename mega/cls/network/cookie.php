<?php
#this class is for control cookies
namespace Mega\Cls\network;
use \Mega\Cls\core as core;

class cookie {
	
	/*
	* check fot cookie is exist
	* @param string $cookie,name of cookie
	* @return boolean (set:true, else:false)
	*/
	public static function check($cookie){
		if(isset($_COOKIE[$cookie]))return true;
		return false;
	}
	/*
	* set value in cookie
	* @param string $cookie,name of cookie
	* @param string $cookieValue,value of cookie
	* @return void
	*/
	public static function set($cookie, $cookieValue){
		$registry = core\registry::singleton();
		$settings = $registry->getPlugin('administrator');
		setcookie($cookie,$cookieValue,time() + $settings->cookie_max_time,'/');
	}
	/*
	* get value from cookie
	* @param string $cookie,name of cookie
	* @return string,value of cookie(not set: null)
	*/
	public static function get($cookie){
		if(isset($_COOKIE[$cookie]))
			return $_COOKIE[$cookie];
		return null;
	}
	
	/*
	 * unset cookie
	 * @param string $key;
	 */
	public function remove($key){
		setcookie($cookie,0,time() - 316137);
	}
}
?>
