<?php
namespace Mega\Apps\menus;
use \Mega\Cls\template as template;

trait addons{
	use \Mega\Apps\Administrator\addons;
	//function is for create menu by other plugins
	protected function createMenu($links,$show_header,$horizontal=FALSE){
		//create an object from raintpl class//
		$raintpl = new template\raintpl;
		//configure raintpl //
		$raintpl->configure('tpl_dir','mega/apps/menus/tpl/');
		$raintpl->assign( "horiz", $horizontal);
		$raintpl->assign( "show_header", $show_header);
		$raintpl->assign( "links", $links);
		//draw and return back content
		return $raintpl->draw('create_menu', true );
	}
	
}
