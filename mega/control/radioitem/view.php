<?php
namespace Mega\Control\radioitem;
use \Mega\Cls\template as template;
use \Mega\Cls\browser as browser;
class view{
	
	private $raintpl;
	
	function __construct(){
		$this->raintpl = new template\raintpl;
		$this->raintpl->configure("tpl_dir","./mega/control/radioitem/");
	}
	public function view_draw($config){
		
		if($config['CSS_FILE'] != ''){ browser\page::addHeader('<link rel="stylesheet" type="text/css" href="' . $config['CSS_FILE']) . '" />';}
		
		$this->raintpl->assign("name",$config['NAME']);
		$this->raintpl->assign("label",$config['LABEL']);
		$this->raintpl->assign("id",$config['ID']);
		$this->raintpl->assign("style",$config['STYLE']);
		$this->raintpl->assign("class",$config['CLASS']);
		$this->raintpl->assign("disabled",$config['DISABLED']);
		$this->raintpl->assign("form",$config['FORM']);
		$this->raintpl->assign("value",$config['VALUE']);
		$this->raintpl->assign("checked",$config['CHECKED']);
		return $this->raintpl->draw("ctr_radioitem",true);
	}
}
?>
