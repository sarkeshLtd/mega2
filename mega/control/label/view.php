<?php
namespace Mega\Control\label;
use \Mega\Cls\template as template;
use \Mega\Cls\browser as browser;
class view{
	private $raintpl;
	
	function __construct(){
		$this->raintpl = new template\raintpl;
		$this->raintpl->configure("tpl_dir",'./mega/control/label/');
	}
	public function view_draw($e){
		$this->raintpl->assign("style",$e['STYLE']);
		$this->raintpl->assign("value",$e['LABEL']);
		$this->raintpl->assign("type",'label label-' . $e['TYPE']);
		$this->raintpl->assign("class",$e['CLASS']);
		$this->raintpl->assign("bs_control",$e['BS_CONTROL']);
		return $this->raintpl->draw("ctr_label",true);
	}
}
?>
