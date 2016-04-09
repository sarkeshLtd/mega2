<?php
namespace Mega\Control\breadcrumb;
class module extends view{
	function __construct(){
		parent::__construct();
	}
	
	public function module_draw($e){
		return $this->view_draw($e);
	}
}
?>
