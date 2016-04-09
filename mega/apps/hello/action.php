<?php
namespace Mega\Apps\hello;
use \Mega\Control as control;
use \Mega\Cls\Database as db;

class action extends module{
	
	function __construct(){
		
	}
	
	/*
	 * action for show hello word
	 * @return array content
	 */
	 public function sample(){
		return [_('First page'),_('Welcome to your site please change home page from basic settings in administrator area.') ];
	 }

}
