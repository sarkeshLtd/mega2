<?php
namespace Mega\Apps\i18n;
use \Mega\Cls\core as core;
use \Mega\Cls\browser as browser;

class event{
	
	/*
	 * action for show hello word
	 * @param array $e, form elements
	 * @return array content
	 */
	public function changeLanguage($e){
		$localize = core\localize::singleton();
		$local = $localize->getLocal($e['lang']['SELECTED']);
		if($localize->setLang($e['lang']['SELECTED']))
			$e['RV']['URL'] = core\general::createUrl([$local->home],$e['lang']['SELECTED']);
		else
			return browser\msg::modal(_('message'), _('changing language has some problem! Please try again later.'),'warning');
		return $e;
	}
}
