<?php
namespace Mega\Apps\menus;
use \Mega\Cls\Database as db;
use \Mega\Cls\browser as browser;
class event{
	use addons;
	function __construct(){}
	
	/*
	 * save add or update new menu
	 * @param array $e,form properties
	 * @return array $e,form properties
	 */
	public function onclickBtnDoMenu($e){
		if($this->hasAdminPanel()){
			if($e['txtName']['VALUE'] != '' && $e['txtHeader']['VALUE'] != '' ){
				$orm = db\orm::singleton();
				$menu = $orm->dispense('menus');
				$update = false;
				if(array_key_exists('hidID', $e)){
					//edite mode
					//check for that menu is exists
					if($orm->count('menus','id=?',[$e['hidID']['VALUE']]) != 0){
						$menu = $orm->findOne('menus','id=?',[$e['hidID']['VALUE']]);
						$update = true;
					}
					else
						return browser\msg::modalEventError($e);
				}
				
				$menu->name = $e['txtName']['VALUE'];
				$menu->localize = $e['cobLang']['SELECTED'];
				$menu->header = $e['txtHeader']['VALUE'];

				$menu->horiz = 0;
				if($e['ckbHorizontal']['CHECKED'] == 1){
					$menu->horiz = 1;
				}

				$menu->show_header = 0;
				if($e['ckbShowHeader']['CHECKED'] == 1){
					$menu->show_header = 1;
				}
				$menuID = $orm->store($menu);
				//add menu to blocks
				if(!$update){
						$block = $orm->dispense('blocks');
						$menus_plg = $orm->findOne('plugins','name=?',['menus']);
						$block->plugin = $menus_plg->id;
						$block->name = $menu->name;
						$block->value = $menuID;
						$block->visual = 1;
						$block->position = 'Off';
						$block->show_header = 0;
						$block->handel = 'drawMenu';
						$orm->store($block);

				}

				return browser\msg::modalSuccessfull($e,['service','administrator','load','menus','listMenus']);
			}
			
			return browser\msg::modalNotComplete($e);
		}
		return browser\msg::modalNoPermission($e);
	}
	
	/*
	 * Delet menu from database
	 * @param array $e,form properties
	 * @return array $e,form properties
	 */
	public function onclickBtnDeleteMenu($e){
		if($this->hasAdminPanel()){
			//first delete block
			$orm = db\orm::singleton();
			$plugin = $orm->findOne('plugins',"name='menus'");
			$orm->exec("DELETE FROM blocks WHERE plugin=? and handel='drawMenu' and value=?;",[$plugin->id,$e['hidID']['VALUE']],NON_SELECT);
			//DELETE MENU
			$orm->exec("DELETE FROM menus WHERE id=?;",[$e['hidID']['VALUE']],NON_SELECT);
			//DELETE LINKS
			$orm->exec("DELETE FROM links WHERE ref_id=?;",[$e['hidID']['VALUE']],NON_SELECT);
			return browser\msg::modalSuccessfull($e,['service','administrator','load','menus','listMenus']);
		}
		return browser\msg::modalNoPermission($e);
	}
	
	/*
	 * Delet link from database
	 * @param array $e,form properties
	 * @return array $e,form properties
	 */
	public function onclickBtnDeleteLink($e){
		if($this->hasAdminPanel()){
			//first delete block
			$orm = db\orm::singleton();
			$orm->exec("DELETE FROM links WHERE id=?;",[$e['hidID']['VALUE']],NON_SELECT);
			return browser\msg::modalSuccessfull($e,['service','administrator','load','menus','listLinks',$e['hidMenuID']['VALUE']]);
		}
		return browser\msg::modalNoPermission($e);
	}
	/*
	 * insert or update link
	 * @param array $e,form properties
	 * @return array $e,form properties
	 */
	public function onclickBtnDoLink($e){
		if($this->hasAdminPanel()){
			if($e['txtLabel']['VALUE'] != '' && $e['txtUrl']['VALUE'] != '' ){
				$orm = db\orm::singleton();
				$link = $orm->dispense('links');
				if(array_key_exists('hidID', $e)){
					//edite mode
					if($orm->count('links','id=?',[$e['hidID']['VALUE']]) != 0)
						$link = $orm->findOne('links','id=?',[$e['hidID']['VALUE']]);
					else
						return browser\msg::modalEventError($e);
				}
				
				$link->label = $e['txtLabel']['VALUE'];
				$link->url = $e['txtUrl']['VALUE'];
				$link->ref_id = $e['hidMenuID']['VALUE'];
				$link->enable = 0;
				if($e['ckbEnable']['CHECKED'] == 1){
					$link->enable = 1;
				}
				$link->rank = $e['cobRank']['SELECTED'];
				$orm->store($link);
				return browser\msg::modalSuccessfull($e,['service','administrator','load','menus','listLinks',$e['hidMenuID']['VALUE']]);
			}
			
			return browser\msg::modalNotComplete($e);
		}
		return browser\msg::modalNoPermission($e);
	}	
}
