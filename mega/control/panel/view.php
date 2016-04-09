<?php
namespace Mega\Control\panel;
use \Mega\Cls\template as template;
use \Mega\Cls\browser as browser;
class view{
	private $raintpl;
	
	function __construct(){
		$this->raintpl = new template\raintpl;
		$this->raintpl->configure("tpl_dir",'./mega/control/panel/');
	}
	public function view_draw($e){
		$this->raintpl->assign("type",$e['TYPE']);
		$this->raintpl->assign("title",$e['TITLE']);
		$this->raintpl->assign("body",$e['BODY']);
		return $this->raintpl->draw("ctr_panel",true);
	}
}
?>
