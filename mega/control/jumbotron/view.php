<?php
namespace Mega\Control\jumbotron;
use \Mega\Cls\template as template;
use \Mega\Cls\browser as browser;
class view{
	
	function __construct(){}
	public function view_draw($e){
        $raintpl = new template\raintpl;
        $raintpl->configure("tpl_dir",'./mega/control/jumbotron/');
        $raintpl->assign("title",$e['TITLE']);
        $raintpl->assign("body",$e['BODY']);
        $raintpl->assign("btn_label",$e['BTN_LABEL']);
        $raintpl->assign("btn_type",$e['BTN_TYPE']);
        $raintpl->assign("btn_url",$e['BTN_URL']);
		return $raintpl->draw("ctrJumbotron",true);
	}
}
?>
