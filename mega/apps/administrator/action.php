<?php
namespace Mega\Apps\Administrator;
use \Mega\Cls\core as core;
use \Mega\Cls\browser as browser;


class action extends module{

	/*
	 * construct
	 */
	function __construct(){
		
	}
	
	/*
	 * show dashboard administrator form
	 * @return string, html content
	 */
	public function dashboard(){
		return $this->moduleDashboard();
	}
	
	/*
	 * check for updates
	 * @return string, html content
	 */
	public function checkUpdate(){
		return $this->moduleCheckUpdate();
	}
	
	/*
	 * show core settings page
	 * @return string, html content
	 */
	public function coreSettings(){
		return $this->moduleCoreSettings();
	}
	
	/*
	 * show manage block form
	 * @return string, html content
	 */
	public function blocks(){
		return $this->moduleBlocks();
	}
	
	/*
	 * show manage localizes
	 * @return string, html content
	 */
	public function basicSettings(){
		return $this->moduleBasicSettings();
	}
	
	/*
	 * edite localize settings
	 * @return string, html content
	 */
	public function basicSettingsEdite(){
		return $this->moduleBasicSettingsEdite();
	}
	
	/*
	 * show form for add new static block
	 * @return string, html content
	 */
	public function newStaticBlock(){
		return $this->moduleNewStaticBlock();
	}
	
	/*
	 * show form for edite
	 * @return string, html content
	 */
	public function editeBlock(){
		return $this->moduleEditeBlock();
	}
	
	/*
	 * edite static block
	 * @return string, html content
	 */
	public function editeStaticBlock(){
		return $this->moduleEditeStaticBlock();
	}
	
	/*
	 * show delete message
	 * @return string, html content
	 */
	public function sureDeleteBlock(){
		return $this->moduleSureDeleteBlock();
	}
	
	/*
	 * show form for manage plugins
	 * @return string, html content
	 */
	public function plugins(){
		return $this->modulePlugins();
	}
	
	/*
	 * show form for manage themes
	 * @return string, html content
	 */
	public function themes(){
		return $this->moduleThemes();
	}
	
	/*
	 * manage regional and languages settings
	 * @return string, html content
	 */
	public function regAndLang(){
		return $this->moduleRegAndLang();
	}
	
}
