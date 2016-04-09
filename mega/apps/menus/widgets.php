<?php
namespace Mega\Apps\menus;


class widgets extends module{
	
	/*
	 * construct
	 */
	function __construct(){}
	
	/*
	 * draw menu in theme
	 * @param string $position , position of block in theme class
	 * @param string $menuID, menu id in database
	 * @return array [title,content]
	 */
	public function drawMenu($position,$menuID){
		return $this->moduleDrawMenu($menuID);
	}

}
