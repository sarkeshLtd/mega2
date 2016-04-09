<?php
namespace Mega\Apps\reports;
use \Mega\Cls\core as core;
use \Mega\Cls\browser as browser;


class action extends module{
	use view;
	/*
	 * construct
	 */
	function __construct(){
		parent::__construct();
	}
	
	/*
	 * show php error log
	 * @return 2D array [title,content]
	 */
	public function phpErrors(){
		return $this->modulePhpErrors();
	}
}
