<?php
namespace Mega\Apps\i18n;
use \Mega\Control as control;

class view {
	
	/*
	 * show select langauge widget
	 * @param object $languages, inserted languages
	 * @return [title,content]
	 */
	protected function viewSelectLanguage($languages){
		$form = new control\form('i18nSelectLanguage');
		$lang = new control\combobox;
		$lang->configure('NAME','lang');
		$lang->configure('LABEL','');
		$lang->configure('TABLE',$languages);
		$lang->configure('SIZE',12);
		$lang->configure('COLUMN_LABELS','language_name');
		$lang->configure('COLUMN_VALUES','language');
		$lang->configure('P_ONCHANGE_PLUGIN','i18n');
		$lang->configure('P_ONCHANGE_FUNCTION','changeLanguage');
		$form->add($lang);
		return array(_('Languages'), $form->draw());
	}
}
