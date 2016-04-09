<?php
namespace Mega\Control\content;
use \Mega\Cls\template as template;
use \Mega\Cls\browser as browser;
class view{
	
	function __construct(){}
	public function view_draw($e){
        $raintpl = new template\raintpl;
        $raintpl->configure("tpl_dir",'./mega/control/content/');
        $raintpl->assign("TITLE",$e['TITLE']);
        $raintpl->assign("BODY",$e['BODY']);
        $raintpl->assign("IMG_SRC",$e['IMG_SRC']);
        $raintpl->assign("href",$e['HREF']);
		return $raintpl->draw("content",true);
	}
}
?>
