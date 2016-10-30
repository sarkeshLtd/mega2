<?php
/*
 * This file get system language and translate all _t() function with po and mo difined files
 * for add tranlations create your language folder in languages like fa_IR
 * step 2 create LC_MESSAGES folder on it and put your mo and po files inside it
 */
$orm = Mega\Cls\Database\orm::singleton();
if($orm->count('localize') != 1)
	define('MULTI_LANG',TRUE);
else
	define('MULTI_LANG',FALSE);

//this function translate text of website
function _t($key){
	$orm = Mega\Cls\Database\orm::singleton();
	$localize = Mega\Cls\Core\localize::singleton();
	if($orm->count('translations','source=? and lang_code=?',[$key,$localize->language()])){
		//return text
		$text = $orm->findOne('translations','source=? and lang_code=?',[$key,$localize->language()]);
		return $text->translated;
	}
	return $key;
}

//SET DEFINES STATIC VARIABLES
$localize = Mega\Cls\Core\localize::singleton();
define('SITE_LANG',$localize->language());
?>
