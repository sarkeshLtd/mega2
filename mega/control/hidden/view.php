<?php
namespace Mega\Control\hidden;
use \Mega\Cls\template as template;
use \Mega\Cls\browser as browser;
class view{
	private $raintpl;
	
	function __construct(){
		$this->raintpl = new template\raintpl;
		$this->raintpl->configure("tpl_dir",'./mega/control/hidden/');
	}
	public function view_draw($e){
		$this->raintpl->assign("name",$e['NAME']);
		$this->raintpl->assign("value",$e['VALUE']);
		$this->raintpl->assign("form",$e['FORM']);
		return $this->raintpl->draw("ctr_hidden",true);
	}
}
?>
