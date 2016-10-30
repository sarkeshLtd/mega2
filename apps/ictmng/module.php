<?php
namespace apps\ictmng;
use Mega\Cls\Browser as browser;
use Mega\Cls\Network as network;
use Mega\Cls\Core as core;
use Mega\cls\Database as db;
use Mega\Apps\Users as Users;

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
		array_push($menu,[$url, _t('Manage forums')]);
		$url = core\general::createUrl(['service','administrator','load','forum','settings']);
		array_push($menu,[$url, _t('Settings')]);

		$ret = [];
		array_push($ret, ['<span class="glyphicon glyphicon-comment" aria-hidden="true"></span>' , _t('Forums')]);
		array_push($ret,$menu);
		return $ret;
	}

	/*
	 * SHOW DASHBOARD OF APPLICATION
	 * @RETURN STR HTML CONTENT
	 */
	public function moduleDashboard(){
		if($this->isLogedin()){
			//get user info
			$user = $this->getCurrentUserInfo();
			return $this->viewDashboard($user);
		}
		return browser\msg::pageAccessDenied();
	}

	/*
	 * submit new PC
	 * @RETURN STR HTML CONTENT
	 */
	public function moduleSubmitNewCase(){
		if($this->isLogedin()){
			//show form
			return $this->viewSubmitNewCase($this->getCurrentUserInfo());
		}
		return browser\msg::pageAccessDenied();
	}

}
