<?php
namespace Mega\Apps\files;
use Mega\Cls\browser as browser;
use Mega\Cls\network as network;
use Mega\Cls\core as core;
use Mega\Cls\Database as db;

class module{
	use view;
	use addons;
	use \Mega\Apps\users\addons;
	
	/*
	 * construct
	 */
	function __construct(){}
	
	//this function return back menus for use in admin area
	public static function coreMenu(){
		$menu = array();
		$url = core\general::createUrl(['service','administrator','load','reports','ports']);
		array_push($menu,[$url, _t('File upload ports')]);
		$url = core\general::createUrl(['service','administrator','load','reports','places']);
		array_push($menu,[$url, _t('Places of files')]);
		$ret = [];
		array_push($ret, ['<span class="glyphicon glyphicon-cloud" aria-hidden="true"></span>' , _t('Files')]);
		array_push($ret,$menu);
		return $ret;
	}
	
	/*
	 * function for do upload file operation
	 * @return string xml content
	 */
	protected function moduleDoUpload(){
		//create roll back object
		$ret = [];
		$ret['msg'] = '0';
		$ret['fileID'] = null;
		$xml = new db\xml($ret);
		if(array_key_exists('uploads',$_FILES)){
			$orm = db\orm::singleton();
			$port = $orm->findOne('file_ports','name=?',[$_REQUEST['port']]);
			//check file size
			$typeValid = false;
			$types = explode(',' , $port->types);
			$fileInfo = new \SplFileInfo($_FILES["uploads"]["name"]);
			$fileExtension = $fileInfo->getExtension();
			foreach($types as $type)
				if(trim($type) == trim($fileExtension) ) $typeValid = true;
			if($_FILES["uploads"]["size"] < $port->maxFileSize && $typeValid){
				$activePlace = $orm->findOne('file_places','state=1');
				$targetDir = $activePlace->options;
				$rndID = core\general::randomString(32,'NC');
				$fileName = $targetDir . $rndID . $_FILES["uploads"]["name"];
				move_uploaded_file($_FILES["uploads"]["tmp_name"],$fileName);
				$file = $orm->dispense('files');
				$file->name = $fileName;
				$file->place = $activePlace->id;
				$file->date = time();
				$file->size = $_FILES["uploads"]["size"];
				$user = $this->getCurrentUserInfo();
				$userID = null;
				if(!is_null($user)) $userID = $user->id;
				$file->user = $userID;
				$file->address = $fileName;
				$file->sid = $rndID;
				$orm->store($file);
				//create roll back object
				$ret['msg'] = $ret['msg'] = browser\page::showBlock(_t('Upload successful!'),_t('Your file uploaded successfuly.'),'MODAL','type-success');
				$ret['fileID'] = $rndID;
			}
			else{
				$ret['msg'] = browser\page::showBlock(_t('File upload fail!'),_t('File size or extension is not match with this type.'),'MODAL','type-warning');
			}
		}
		return $xml->arrayToXml($ret, "root");
	}
	
	/*
	 * this service return back and show file
	 * @return image file and ...
	 */
	protected function moduleLoad(){
		$orm = db\orm::singleton();
		if($orm->count('files','sid=?',[PLUGIN_OPTIONS]) != 0){
			$file = $orm->findOne('files','sid=?',[PLUGIN_OPTIONS]);
			return $this->moduleLoadFile($file);
		}
	}
	
	/*
	 * find file extention and send back on browser
	 * @return null
	 */
	protected function moduleLoadFile($file){
		$fileInfo = pathinfo(APP_PATH . $file->name);
		//check and send back files
		if($fileInfo['extension'] == 'png'){
			//show png picture
			header('Content-Type: image/png');
		}
		elseif($fileInfo['extension'] == 'jpg' || $fileInfo['extension'] == 'jpeg'){
			//show jpg image
			header('Content-Type: image/jpeg');
		}
		elseif($fileInfo['extension'] == 'gif'){
			//show jpg image
			header('Content-Type: image/gif');
		}
		
		else{
			//download file
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment, filename=" . $file->name );
		}
		readfile($file->name);
		return null;
	}
	
	/*
	 * this service remove file
	 * @param string $sid, special file id
	 * @return image file and ...
	 */
	protected function moduleRemoveFile(){
		if(array_key_exists('sid',$_REQUEST)){
			$orm = db\orm::singleton();
			if($this->fileRemove($_REQUEST['sid']))
				return 'OK';
		}
		return 'fail';
	}
}
