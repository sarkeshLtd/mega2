<?php
namespace Mega\Apps\i18n;
use \Mega\Control as control;

class widgets extends module{
	
	/*
	 * show form for change site language
	 * @return array [title,content]
	 */
	public function selectLanguage(){
		return $this->moduleSelectLanguage();
	}
}
