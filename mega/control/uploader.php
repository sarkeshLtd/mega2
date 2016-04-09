<?php
namespace Mega\Control;
use \Mega\Control as control;
class uploader extends control\uploader\module{
	private $config;
	
	function __construct($name = null){
		parent::__construct();
		
		//this config save name of control
		$this->config['NAME'] = 'uploader';
		if(!is_null($name)) $this->config['NAME'] = $name;
		
		//for add class for change style of control use this config
		$this->config['CLASS'] = '';
		
		//Set css files to header of page with this config
		$this->config['CSS_FILE'] = '';
		
		
		//this config save form name that controll added
		$this->config['FORM'] = 'Form';
		
		//label is a text that show at the abow of element
		$this->config['LABEL'] = _('File uploader');
		
		//help is a text that show on control for take some note to user
		$this->config['HELP'] = _('Select file and click on upload button.');
		
		//this config set width of controll that most be between 1 and 12 
		//default value is 12 (full width)
		$this->config['SIZE'] = 12;
		
		//this config set max size of file that can be uploaded 
		//unit of this number is byte
		$this->config['MAX_FILE_SIZE'] = 65536; //Byte
		
		//this id for set id that plugin get from file system
		$this->config['FILE_SYSTEM_ID'] = '0'; 

		//if you  enable this option drag and drop will enabled in system
		$this->config['CAN_DRAG'] = false;

		//for set number of files that user can upload ,use this config . default value is 1 and can be between 1 and 15
		// 0  is not valid number for this config
		$this->config['MAX_FILE_NUMBER'] = '1';
		
		//for set type of files that user can upload set this config
		//file types should be seperate with (,) 
		$this->config['FILE_TYPES'] = "jpg, gif, png";
		
		//if user want to upload picture. it's better for show preview of picture to user.
		//this config is boolean and default of that is false (not enabled by default)
		$this->config['PREVIEW'] = true;
		
		//Use this config for add javascript files to header of page
		$this->config['SCRIPT_SRC'] = '';
		
		//type is config for show block , success info danger primary 
		$this->config['TYPE'] = 'default';

        //SAVE VALUE OF UPLOADER
            $this->config['VALUE'] = '';
	}
	
	//this function configure control//
	
	public function configure($key, $value){
		// checking for that key is exists//
		if(key_exists($key, $this->config)){
			//check for type		
			$this->config[$key] = $value;
			return TRUE;
		}
		//key not exists//
		
		return FALSE;
	}
	
	//this function return back control that compiled
	public function draw(){
		return $this->module_draw($this->config);
	}
	
	
	//use this function for get value of configures of this control
	public function get($key){
		if(key_exists($key, $this->config)){
			return $this->config[$key];
		}
		die('Index is out of range');
	}
	
	/*
	 * function use for set configs like object
	 * @param strin $key, key of config
	 * @param string $value, value of config
	 * @return boolean result
	 */
	public function __set($key,$value){
		$key = strtoupper($key);
		if(key_exists($key, $this->config)){		
			$this->config[$key] = $value;
			return TRUE;
		}
		return FALSE;
	}
	
}
?>
