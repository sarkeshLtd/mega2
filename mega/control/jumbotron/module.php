<?php
namespace Mega\Control\jumbotron;
class module extends view{
	function __construct(){
		parent::__construct();
	}
	
	public function module_draw($config){
		return $this->view_draw($config);
	}
}
?>
