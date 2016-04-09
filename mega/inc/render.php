<?php
#this function render theme file and replace contents with tags that defined in that.
function render($buffer){
	/* replace headers 
	* like css java scripts and ect
	*/
	$buffer = str_replace("</#PAGE_TITTLE#/>", \Mega\Cls\browser\page::getPageTitle(), $buffer);
	//LOAD HEADERS
	$buffer = str_replace("</#HEADERS#/>",  \Mega\Cls\browser\page::loadHeaders(false), $buffer);
	$buffer = str_replace("</#SITE_NAME#/>", \Mega\Cls\browser\page::getPageTitle(), $buffer);
	$buffer = str_replace("</#SITE_DOMAIN#/>", DOMAIN_EXE, $buffer);
	return $buffer;
}
?>
