<?php
namespace Mega\Apps\menus;
use \Mega\Cls\core as core;
use \Mega\Cls\Database as db;
use \Mega\Cls\browser as browser;
class module extends view{
	use addons;
	function __construct(){}
	
	/*
	 * ADD menus to administrator area
	 * @return 2D array
	 */
	public static function coreMenu(){
		$menu = array();
		$url = core\general::createUrl(['service','administrator','load','menus','doMenu']);
		array_push($menu,[$url, _('New menu')]);
		$url = core\general::createUrl(['service','administrator','load','menus','listMenus']);
		array_push($menu,[$url, _('List menus')]);
		$ret = array();
		array_push($ret, ['<span class="glyphicon glyphicon-align-justify" aria-hidden="true"></span>' , _('Menus')]);
		array_push($ret,$menu);
		return $ret;
	}
	
	/*
	 * show list of menus
	 * @return array [title,content]
	 */
	public function moduleListMenus(){
		if($this->hasAdminPanel()){
			$orm = db\orm::singleton();
			return $this->viewListMenus($orm->findAll('menus'));
		}
		return browser\msg::pageAccessDenied();
	}
	
	/*
	 * insert or edite menu
	 * @return array [title,content]
	 */
	public function moduleDoMenu(){
		$options = explode('/',PLUGIN_OPTIONS);
		if($this->hasAdminPanel()){
			$localize = core\localize::singleton();
			if(count($options) == 2){
				//new mode
				return $this->viewDoMenu($localize->getAll());
			}
			else{
				//edite menu
				$orm = db\orm::singleton();
				if($orm->count('menus','id=?',[$options[2]]) != 0){
					$menu = $orm->findOne('menus','id=?',[$options[2]]);
					return $this->viewDoMenu($localize->getAll(),$menu);
				}
			}
		}
		return browser\msg::pageAccessDenied();	
	}
	
	/*
	 * Show message for delete menu
	 * @return array [title,content]
	 */
	public function moduleSureDeleteMenu(){
		$options = explode('/',PLUGIN_OPTIONS);
		if($this->hasAdminPanel()){
			if(count($options) == 3){
				$orm = db\orm::singleton();
				if($orm->count('menus','id=?',[$options[2]]) != 0)
					return $this->viewSureDeleteMenu($orm->load('menus',$options[2]));
				return browser\msg::pageNotFound();
			}
			return browser\msg::pageError();
		}
		return browser\msg::pageAccessDenied();	
	}
	
	/*
	 * show list of links in menu
	 * @return array [title,content]
	 */
	public function moduleListLinks(){
		$options = explode('/',PLUGIN_OPTIONS);
		if($this->hasAdminPanel()){
			if(count($options) == 3){
				$orm = db\orm::singleton();
				if($orm->count('menus','id=?',[$options[2]]) != 0)
					return $this->viewListLinks($orm->find('links','ref_id=?',[$options[2]]),$options[2]);
				return browser\msg::pageNotFound();
			}
			return browser\msg::pageError();
		}
		return browser\msg::pageAccessDenied();
	}
	
	/*
	 * Show message for delete links
	 * @return array [title,content]
	 */
	public function moduleSureDeleteLink(){
		$options = explode('/',PLUGIN_OPTIONS);
		if($this->hasAdminPanel()){
			if(count($options) == 3){
				$orm = db\orm::singleton();
				if($orm->count('links','id=?',[$options[2]]) != 0)
					return $this->viewSureDeleteLink($orm->load('links',$options[2]));
				return browser\msg::pageNotFound();
			}
			return browser\msg::pageError();
		}
		return browser\msg::pageAccessDenied();
	}
	
	/*
	 * draw menu in theme
	 * @param string $menuID, menu id in database
	 * @return array [title,content]
	 */
	public function moduleDrawMenu($menuID){
		$orm = db\orm::singleton();
		$links = $orm->exec('SELECT l.id,l.label,l.url,l.rank,l.enable,m.name,m.show_header,m.header,m.horiz FROM links l INNER JOIN menus m ON l.ref_id=m.id WHERE m.id=? ORDER BY l.rank',[$menuID]);
		return self::viewDrawMenu($links);
	}
	
	/*
	 * insert or update link
	 * @return array [title,content]
	 */
	public function moduleDoLink(){
		$options = explode('/',PLUGIN_OPTIONS);
		if($this->hasAdminPanel()){
			if(count($options) == 4){
				$orm = db\orm::singleton();
				if($orm->count('links','id=?',[$options[3]]) != 0)
					return $this->viewDoLink($orm->load('links',$options[3]),$options[2]);
				return browser\msg::pageNotFound();
			}
			elseif(count($options) == 3){
				return $this->viewDoLink(null,$options[2]);
			}
			return browser\msg::pageError();
		}
		return browser\msg::pageAccessDenied();	
	}
	
}
