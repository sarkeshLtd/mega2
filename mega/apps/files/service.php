<?php
namespace Mega\Apps\files;
use \Mega\Cls\core as core;

class service extends module{
	
	function __construct(){}
	
	
	/*
	 * function for do upload file operation
	 * @return string xml content
	 */
	public function doUpload(){
		return $this->moduleDoUpload();
	}
	
	/*
	 * this service return back and show file
	 * @return image file and ...
	 */
	public function load(){
		return $this->moduleLoad();
	}
	
	/*
	 * this service remove file
	 * @param string $sid, special file id
	 * @return image file and ...
	 */
	public function removeFile(){
		return $this->moduleRemoveFile();
	}
}
