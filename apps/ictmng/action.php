<?php
namespace apps\ictmng;
use \Mega\cls\core as core;
use \Mega\cls\browser as browser;
use \mega\apps\users;

class action extends module{

	/*
	 * construct
	 */
	function __construct(){
		parent::__construct();
	}
	
	/*
	 * show dashboard of application
	 * @RETURN STR HTML CONTENT
	 */
	public function dashboard(){
		return $this->moduleDashboard();
	}

	/*
	 * submit new PC
	 * @RETURN STR HTML CONTENT
	 */
	public function submitNewCase(){
		return $this->moduleSubmitNewCase();
	}
}

