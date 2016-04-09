<?php
namespace Mega\Apps\i18n;
use \Mega\Control as control;

class module extends view{
	use addons;
	/*
	 * show form for change site language
	 * @return array [title,content]
	 */
	protected function moduleSelectLanguage(){
		return $this->viewSelectLanguage($this->getLanguages());
	}
}
