<?php
namespace Mega\Apps\i18n;
use \Mega\Cls\Database as db;
use \Mega\Cls\core as core;
trait addons {
	
	/*
	 * get all languages with ordered by
	 * @return array of languages
	 */
	public function getLanguages(){
		$orm = db\orm::singleton();
		$localize = core\localize::singleton();
		return $orm->exec("SELECT language,language_name FROM localize ORDER BY language=? DESC;", [$localize->language()],SELECT);
	}
}
