<?php
// this class is for control localize and multi language support in system
namespace Mega\Cls\Core;
use \Mega\Cls\Network as network;
use \Mega\Cls\Database as db;
use \Mega\Cls\Patterns as patterns;

class localize{
	use patterns\singleton;
	/*
	 * @var object of database
	 */
	private $orm;
	
	/*
	 * @var object,localize class
	 */
	private $localize;
	
	/*
	* construct
	*/
	function __construct(){
		$this->orm = db\orm::singleton();
		$this->localize = $this->orm->findOne('localize','main=?',[1]);
	}
	/*
	* this function return difined localize settings in cookie
	* @param boolean $default(default language:true, else:false)
	* @return array
	*/
	public function localize($default = false){
		if($default) return $this->localize;
		return $this->orm->findOne('localize','language=?',[$this->language()]);
	}
	
	/*
	* this function return difined localize settings in cookie
	* @param string $language,language code like en_US or fa_IR
	* @return array
	*/
	public function getLocal($lang){
		return $this->orm->findOne('localize','language=?',[$this->language()]);
	}

	/*
	* set cms language on cookie
	* @param strin $lang, language code
	* @return boolean,(successful:true else:false)
	*/
	public function setLang($lang){
		if($lang != ''){
			network\cookie::set('siteLanguage' , $lang);
			network\session::set('siteLanguage', $lang);
			return true;
		}
		return false;
	}
	
	/*
	* get language name from cookie if that not defined return system default localize language
	* @return void
	*/
	public function language(){
		if(isset($_SESSION['siteLanguage'])) return $_SESSION['siteLanguage'];
		elseif(defined('LOCALIZE')) return LOCALIZE;	
		elseif(isset($_COOKIE['siteLanguage'])) return $_COOKIE['siteLanguage'];		
		return $this->localize->language;
	}
	
	/*
	* return all localize that exists in system
	* @return array dbRow object
	*/
	public function getAll(){
		return $this->orm->findAll('localize');
	}
	
	/*
	* return default language of system
	* @return dbRow object
	*/
	public function defLanguage(){
		return $this->localize;
	}
	
	/*
	 * check for that website is multi language
	 * @return boolean(multilanguage: true, else false)
	 */
	public function isMultiLang(){
		$orm = db\orm::singleton();
		if($orm->count('localize') != 1)
			return true;
		return false;
	}

	/*
	 * return all active languages
	 * @RETURN ARRAY,all language codes
	 */
	public function getAllLanguageCodes(){
		$orm = db\orm::singleton();
		return $orm->getColumn('localize','language');

	}
	
}
?>
