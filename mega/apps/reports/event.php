<?php
namespace Mega\Apps\reports;
use \Mega\Cls\browser as browser;
use \Mega\Cls\core as core;

class event extends module{
	
	/*
	 * clear php errors
	 * @param array $e, form properties
	 * @return array, form properties
	 */
	public function onclickBtnClearPhpErrors($e){
		unlink(ERRORS_LOG_PLACE);
		return browser\msg::modalSuccessfull($e,['service','administrator','load','administrator','dashboard']);
	}
}
