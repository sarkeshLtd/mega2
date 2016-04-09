<?php
namespace Mega\Control\breadcrumb;
use \Mega\Cls\template as template;
use \Mega\Cls\browser as browser;
class view{
	private $raintpl;
	
	function __construct(){
		$this->raintpl = new template\raintpl;
		$this->raintpl->configure("tpl_dir",'./mega/control/breadcrumb/');
	}
	public function view_draw($e){
		$this->raintpl->assign("links",$e);
		return $this->raintpl->draw("breadcrumb",true);
	}
}
?>
