<?php
namespace Mega\Apps\menus;
class action extends module{
	
	function __construct(){}
	
	/*
	 * show list of menus
	 * @return array [title,content]
	 */
	public function listMenus(){
		return $this->moduleListMenus();
	}
	
	/*
	 * insert or edite menu
	 * @return array [title,content]
	 */
	public function doMenu(){
		return $this->moduleDoMenu();
	}
	
	/*
	 * Show message for delete menu
	 * @return array [title,content]
	 */
	public function sureDeleteMenu(){
		return $this->moduleSureDeleteMenu();
	}
	
	/*
	 * show list of links in menu
	 * @return array [title,content]
	 */
	public function listLinks(){
		return $this->moduleListLinks();
	}
	
	/*
	 * Show message for delete links
	 * @return array [title,content]
	 */
	public function sureDeleteLink(){
		return $this->moduleSureDeleteLink();
	}
	
	/*
	 * insert or update link
	 * @return array [title,content]
	 */
	public function doLink(){
		return $this->moduleDoLink();
	}
	
}
