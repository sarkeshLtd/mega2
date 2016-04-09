<?php
namespace Mega\Control\row;
use \Mega\Cls\template as template;
use \Mega\Cls\browser as browser;
class view{
	
	private $raintpl;
	
	function __construct(){
		$this->raintpl = new template\raintpl;
		$this->raintpl->configure("tpl_dir","./mega/control/row/");
	}
	public function view_draw($e,$config){
		$this->raintpl->assign("in_table",$config['IN_TABLE']);
		$this->raintpl->assign("size",$config['SIZE']);
		$this->raintpl->assign("e",$e);
		$this->raintpl->assign("VERTICAL_ALIGN",$config['VERTICAL_ALIGN']);
		$this->raintpl->assign("pad_up",$config['PADDING_UP']);
		if($config['IN_TABLE']){
			return $this->raintpl->draw("ctr_row_in_table",true);
		}
		else{
			return $this->raintpl->draw("ctr_row_simple",true);
		}
	}
}
?>
