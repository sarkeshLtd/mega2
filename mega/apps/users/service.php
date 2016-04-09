<?php
namespace Mega\Apps\users;
use \Mega\cls\core as core;

class service extends module{
	
	function __construct(){
		
	}
	
	/*
	 * show login page in service mode
	 * @return string html content
	 */
	public function login(){
		if(!$this->isLogedin())
			return $this->moduleLoginSinglePage();
		return core\router::jump([PLUGIN_OPTIONS]);
	}
	
	/*
	 * webservice for login user to system
	 * @return string (0=incerrect username or password, else:string is special login key)
	 */
	public function WsLogin(){
		if(defined('PLUGIN_OPTIONS')){
			$options = explode('/',PLUGIN_OPTIONS);
			if(count($options) == 2)
				return $this->moduleWsLogin($options[0],$options[1]);
		}
		return 0;
	}
	
	/*
	 * FUNCTION FOR CHECK USER SPECIAL KEY
	 * @return integer(1= is loged in,2=not valid)
	 */
	public function WsCheckLogin(){
		if(defined('PLUGIN_OPTIONS'))
			return $this->moduleWsCheckLogin();
	}
	
	
}
