<?php
namespace Mega\Control\uploader;
use \Mega\Cls\Database as db;

class module extends view{
	function __construct(){
		parent::__construct();
	}
	
	protected function module_draw($config){
		//save places
		$orm = db\orm::singleton();
		if($orm->count('file_ports','name=?',[$config['FORM'] . $config['NAME']]) == 0){
			$port = $orm->dispense('file_ports');
			$port->name = $config['FORM'] .	 $config['NAME'];
			$port->types = $config['FILE_TYPES'];
			$port->maxFileSize = $config['MAX_FILE_SIZE'];
			$orm->store($port);
		}
		//set file size unit
		if($config['MAX_FILE_SIZE'] < 1023){
			$config['FILE_UNIT'] = _t('Byte');
			$config['MAX_FILE_SIZE_UNIT'] = $config['MAX_FILE_SIZE'];
		}
		elseif($config['MAX_FILE_SIZE'] < 1048576){
			$config['FILE_UNIT'] = _t('KByte');
			$config['MAX_FILE_SIZE_UNIT'] = round($config['MAX_FILE_SIZE']/1024);
		}
		else{ 
			$config['FILE_UNIT'] = _t('MByte');
			$config['MAX_FILE_SIZE_UNIT'] = round($config['MAX_FILE_SIZE']/1048576);
			}
		return $this->view_draw($config);
	}
	
}
?>
