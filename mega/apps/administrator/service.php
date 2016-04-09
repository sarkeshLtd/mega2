<?php
namespace Mega\Apps\Administrator;


class service extends module{
	
	function __construct(){
		
	}
	
	/*
	 * for load basic html panel
	 * @param string $opt, option of action
	 * @return string, html content
	 */
	public function load(){
		return $this->moduleLoad(PLUGIN_OPTIONS);
	}
}
