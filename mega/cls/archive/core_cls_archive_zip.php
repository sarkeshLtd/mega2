<?php
/*
* Class for working with zip archives
*/
namespace Mega\Cls\archive;
class zip{
	/*
	* var object ZipArchive
	*/
	private $zip;
	/*
	* constructor
	* @fileName string,full file name with address
	*/
	
	function __construct($fileName){
		$this->zip = new \ZipArchive;
		if ($this->zip->open($fileName) === FALSE) {
			exit( _('Can not open zip archive files'));
		}
	}
	
	/*
	* destructor
	*/
	function __destruct(){
		//close zip file
		@$this->zip->close();
	}
	/*
	* extract zip archive
	* @adr string,address of folder
	* @return boolean(true:success , false:fail)
	*/
	public function extract($adr){
		if(is_dir($adr)){
			$this->zip->extractTo($adr);
			return true;
		}
		return false;
	}
}
?>
