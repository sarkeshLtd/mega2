<?php
namespace Mega\Control\radiobuttons;

use \Mega\Cls\template as template;
use \Mega\Cls\browser as browser;

class view{
	
	private $raintpl;
	
	function __construct(){
		$this->raintpl = new  template\raintpl;
		$this->raintpl->configure("tpl_dir","./mega/control/radiobuttons/");
	}
	public function view_draw($e,$config){
		$this->raintpl->assign("e",$e);
		$this->raintpl->assign("size",$config['SIZE']);
		$this->raintpl->assign("label",$config['LABEL']);
		$this->raintpl->assign("inline",$config['INLINE']);
		$this->raintpl->assign("help",$config['HELP']);
		return $this->raintpl->draw("ctr_radiobuttons",true);
	}
}
?>
