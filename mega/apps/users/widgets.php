<?php
namespace Mega\Apps\users;


class widgets extends module{
	use view;
	
	/*
	 * construct
	 */
	function __construct(){}
	
	/*
	 * show login form
	 * @return string, html content
	 */
	public function login(){
		return $this->moduleFrmLogin('block');
	}
	
	/*
	 * show minimal profile in widget mode
	 * @return string, html content
	 */
	public function profile(){
		return $this->moduleWidgetProfile();
	}

}
