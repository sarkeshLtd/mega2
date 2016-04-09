<?php
namespace Mega\Control\checkbox;
class module extends view{
	
	function __construct(){
		 parent::__construct();
	}
	
	protected function module_draw($config, $show){
		return $this->view_draw($config, $show);
	}
	
}
