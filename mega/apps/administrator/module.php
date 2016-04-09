<?php
namespace Mega\Apps\Administrator;
use \Mega\Cls\Database as db;
use \Mega\Cls\browser as browser;
use \Mega\Cls\core as core;

class module extends view{
	use addons;

	/*
	 * construct
	 */
	function __construct(){
		
	}
	
	//this function return back menus for use in admin area
	public static function coreMenu(){
		$menu = array();
		$url = core\general::createUrl(['service','administrator','load','administrator','plugins']);
		array_push($menu,[$url, _('Plugins')]);
		$url = core\general::createUrl(['service','administrator','load','administrator','regAndLang']);
		array_push($menu,[$url, _('Regional and Languages')]);
		$url = core\general::createUrl(['service','administrator','load','administrator','basicSettings']);
		array_push($menu,[$url, _('Basic Settings')]);
		$url = core\general::createUrl(['service','administrator','load','administrator','themes']);
		array_push($menu,[$url, _('Apperance')]);
		$url = core\general::createUrl(['service','administrator','load','administrator','blocks']);
		array_push($menu,[$url, _('Blocks')]);
		$url = core\general::createUrl(['service','administrator','load','administrator','coreSettings']);
		array_push($menu,[$url, _('Core Settings')]);
		$url = core\general::createUrl(['service','administrator','load','administrator','checkUpdate']);
		array_push($menu,[$url, _('Update Center')]);
		$ret = array();
		array_push($ret, ['<span class="glyphicon glyphicon-tasks" aria-hidden="true"></span>' , _('Administrator')]);
		array_push($ret,$menu);
		return $ret;
	}
	
	/*
	 * load basic administrator panel
	 * @param string $opt, option of action
	 * @return array, html content
	 */
	protected function moduleLoad($opt){
		if($this->isLogedin()){
			if($this->hasPermission('adminPanel')){
				$opt = explode('/',$opt);
				$registry = core\registry::singleton();
				$router = new core\router($opt[0],$opt[1]);
				$content = $router->showContent(false);
				return $this->viewLoad($this->getMenus(),$content,$this->getCurrentUserInfo(),$registry->getPlugin('administrator'));
			}
			//show access denied message
			return browser\msg::serviceAccessDenied();
		}
		return core\router::jump(['service','users','login','service/administrator/load/administrator/dashboard']);
	}
	
	/*
	 * show dashboard administrator form
	 * @return array, html content
	 */
	protected function moduleDashboard(){
		if($this->hasAdminPanel())
			return $this->viewDashboard();
		return browser\msg::pageAccessDenied();
	}
	
	/*
	 * check for updates
	 * @return array, html content
	 */
	protected function moduleCheckUpdate(){
		if($this->hasAdminPanel()){
			$registry = core\registry::singleton();
			return $this->viewCheckUpdate($registry->get('administrator','build_num'),file_get_contents( UPDATE_SERVER . '/last_version.txt'));
		}
		return browser\msg::pageAccessDenied();
	}
	
	/*
	 * show core settings page
	 * @return array, html content
	 */
	protected function moduleCoreSettings(){
		if($this->hasAdminPanel()){
			$registry = core\registry::singleton();
			return $this->viewCoreSettings($registry->getPlugin('administrator'));
		}
		return browser\msg::pageAccessDenied();		
	}
	
	/*
	 * show manage block form
	 * @return array, html content
	 */
	protected function moduleBlocks(){
		if($this->hasAdminPanel()){
			//get all blocks from database 
			$sql = "SELECT b.id, b.plugin, p.id AS 'plugin_id', b.name as 'block_name', b.position, b.rank, b.handel, b.visual , p.name FROM blocks b INNER JOIN plugins p ON b.plugin=p.id WHERE b.name != 'content';";
			$orm = db\orm::singleton();
			$blocks = $orm->exec($sql,[],SELECT);
			//get placess from active theme
			$theme = $this->activeTheme();
			//get places from theme file
			$themeAdr = '\\themes\\' . $theme;
			$themeObj = new $themeAdr;
			$places = $themeObj->getPlaces();
			return $this->viewBlocks($blocks,$places);
		}
		return browser\msg::pageAccessDenied();	
		
	}
	
	/*
	 * show manage block form
	 * @return array, html content
	 */
	protected function moduleBasicSettings(){
		if($this->hasAdminPanel()){
			//get all localize from database
			$orm = db\orm::singleton();
			$locals = $orm->findAll('localize');
			return $this->viewBasicSettings($locals);
		}
		return browser\msg::pageAccessDenied();
	}
	
	/*
	 * edite localize settings
	 * @return array, html content
	 */
	protected function moduleBasicSettingsEdite(){
		$options = explode('/',PLUGIN_OPTIONS);
		if(count($options) == 3){
			if($this->hasAdminPanel()){
				//get all localize from database
				$orm = db\orm::singleton();
				return $this->viewBasicSettingsEdite($orm->findOne('localize','id=?',[$options[2]]));
			}
			return browser\msg::pageAccessDenied();	
		}
		return browser\msg::pageError();
	}
	
	/*
	 * show form for add new static block
	 * @return array, html content
	 */
	protected function moduleNewStaticBlock(){
		if($this->hasAdminPanel()){
			return $this->viewNewStaticBlock();
		}
		return browser\msg::pageAccessDenied();		
	}
	
	/*
	 * show form for edite
	 * @return array, html content
	 */
	protected function moduleEditeBlock(){
		$options = explode('/',PLUGIN_OPTIONS);
		if(count($options) == 3){
			if($this->hasAdminPanel()){
				//check for that is id cerrect
				$orm = db\orm::singleton();
				if($orm->count('blocks','id=?',[$options[2]]) != 0){
				
					//get locations from theme file
					$activeTheme = $this->activeTheme();
					$places = array();
					if(method_exists('\\themes\\' . $activeTheme,'getPlaces'))
						$places = call_user_func(array('\\themes\\' . $activeTheme,'getPlaces'),'content');
					array_push($places,'Off');
					
					//id is cerrect
					$block = $orm->findOne('blocks','id=?',[$options[2]]);
					//get all localizes
					$locals = $orm->findAll('localize');
					$languages = [];
					foreach ($locals as $key => $local)
						array_push($languages, [$local->language_name,$local->language]);
					//add all block
					array_push($languages, [_('All languages'),'all']);
					return $this->viewEditeBlock($block,$places,$languages);
				}
				return browser\msg::pageNotFound();
			}
			return browser\msg::pageAccessDenied();	
		}
		return browser\msg::pageError();
	}
	
	/*
	 * edite static block
	 * @return array, html content
	 */
	protected function moduleEditeStaticBlock(){
		$options = explode('/',PLUGIN_OPTIONS);
		if(count($options) == 3){
			if($this->hasAdminPanel()){
				//check for that is id cerrect
				$orm = db\orm::singleton();
				if($orm->count('blocks','id=?',[$options[2]]) != 0)
					return $this->viewNewStaticBlock($orm->findOne('blocks','id=?',[$options[2]]));
				return browser\msg::pageNotFound();
			}
			return browser\msg::pageAccessDenied();	
		}
		return browser\msg::pageError();
	}
	
	/*
	 * show delete message
	 * @return array, html content
	 */
	protected function moduleSureDeleteBlock(){
		$options = explode('/',PLUGIN_OPTIONS);
		if(count($options) == 3){
			if($this->hasAdminPanel()){
				//check for that is id cerrect
				$orm = db\orm::singleton();
				if($orm->count('blocks','id=? and visual=?',[$options[2],1]) != 0)
					return $this->viewSureDeleteBlock($orm->findOne('blocks','id=?',[$options[2]]));
				return browser\msg::pageNotFound();
			}
			return browser\msg::pageAccessDenied();	
		}
		return browser\msg::pageError();
	}
	
	/*
	 * show form for manage plugins
	 * @return array, html content
	 */
	protected function modulePlugins(){
		if($this->hasAdminPanel()){
			//refresh plugins
			$this->refreshPlugins();
			//get all localize from database
			$orm = db\orm::singleton();
			$plugins = $orm->find('plugins','can_edite != 0');
			return $this->viewPlugins($plugins);
		}
		return browser\msg::pageAccessDenied();
	}
	
	/*
	 * show form for manage themes
	 * @return string, html content
	 */
	public function moduleThemes(){
		if($this->hasAdminPanel()){
			//Get all themes that exists
			$directory = scandir(APP_PATH. 'Themes/');
			$themes = (array) null;
			foreach($directory as $files)
				if(is_dir(APP_PATH . 'Themes/' . $files) && $files != '.' && $files != '..')
					array_push($themes,$files);
			//get current active theme
			$activeTheme = $this->activeTheme();
			//send to view for show themes
			return $this->viewThemes($themes,$activeTheme);
		}
		return browser\msg::pageAccessDenied();
	}
	
	/*
	 * manage regional and languages settings
	 * @return string, html content
	 */
	public function moduleRegAndLang(){
		if($this->hasAdminPanel()){
			$orm = db\orm::singleton();
			//get default country
			$registry = new core\registry;
			$settings = $registry->getPlugin('administrator');
			//get all countneries
			$countries = $orm->exec('SELECT * FROM countries ORDER BY country_name=? DESC',[$settings->default_country]);
			
			//load default timezone
			$timezones = $orm->exec('SELECT * FROM  timezones ORDER BY timezone_name=? DESC',[$settings->default_timezone]);
			
			//get localize
			$localize = new core\localize;
			$locals = $localize->getAll();
			
			return $this->viewRegAndLang($countries,$timezones,$locals,$localize->localize(true));
			
		}
		return browser\msg::pageAccessDenied();
	}
}
