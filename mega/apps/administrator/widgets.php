<?php
namespace Mega\Apps\Administrator;


class widgets extends module{
	
	/*
	 * construct
	 */
	function __construct(){}
	
	/*
	 * show static blocks
	 * @param string $position , position of block in theme class
	 * @param string $value, value of block
	 * @return array [title,content]
	 */
	public function staticBlock($position, $value){
		return explode('<::::>',$value);
	}

}
