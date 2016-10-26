<?php
namespace apps\ictmng;
use Mega\Cls\Browser as browser;
use Mega\Cls\Network as network;
use Mega\Cls\Core as core;
use Mega\cls\Database as db;

class module extends view{
	use addons;
	
	/*
	 * construct
	 */
	function __construct(){}
	
	//this function return back menus for use in admin area
	public static function coreMenu(){
		$menu = array();
		$url = core\general::createUrl(['service','administrator','load','forum','listForums']);
		array_push($menu,[$url, _('Manage forums')]);
		$url = core\general::createUrl(['service','administrator','load','forum','settings']);
		array_push($menu,[$url, _('Settings')]);

		$ret = [];
		array_push($ret, ['<span class="glyphicon glyphicon-comment" aria-hidden="true"></span>' , _('Forums')]);
		array_push($ret,$menu);
		return $ret;
	}
	

}
