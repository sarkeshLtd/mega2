<?php
namespace Mega\Control\tile;

class module extends view{
	function __construct(){
		parent::__construct();
	}
	
	public function module_draw($items,$config,$places){
		return $this->view_draw($items,$config,$places);
	}
	
}
?>
