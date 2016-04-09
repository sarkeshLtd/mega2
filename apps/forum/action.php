<?php
namespace apps\forum;
use \Mega\cls\core as core;
use \Mega\cls\browser as browser;


class action extends module{

	/*
	 * construct
	 */
	function __construct(){
		parent::__construct();
	}
	
	/*
	 * show list of forums
	 * @RETURN html content [title,body]
	 */
	public function listForums(){
		if($this->isLogedin())
			return $this->moduleListForums();
		return core\router::jump(['service','users','login','service/administrator/load/forum/listForums']);
	}
	
	/*
	 * show form for add new forum
	 * @RETURN html content [title,body]
	 */
    public function newForum(){
        if($this->isLogedin())
            return $this->moduleNewForum();
        return core\router::jump(['service','users','login','service/administrator/load/forum/newForum']);
    }
    
    /*
	 * show form for edite exists forum
	 * @RETURN html content [title,body]
	 */
    public function editeForum(){
        if($this->isLogedin())
            return $this->moduleEditeForum();
        return core\router::jump(['service','users','login','service/administrator/load/forum/listForums']);
    }

    /*
	 * show form for delete forum
	 * @RETURN html content [title,body]
	 */
    public function sureDeleteforum(){
        if($this->isLogedin())
            return $this->moduleSureDeleteforum();
        return core\router::jump(['service','users','login','service/administrator/load/forum/listForums']);
    }

    /*
	 * Post new topic
	 * @RETURN html content [title,body]
	 */
    public function newTopic(){
        if($this->isLogedin())
            return $this->moduleNewTopic();
        return browser\msg::pageAccessDenied();
    }

    /*
	 * show main page of forum
	 * @RETURN html content [title,body]
	 */
    public function main(){
        return $this->moduleMain();
    }

    /*
    * show topic
    * @RETURN html content [title,body]
    */
    public function showTopic(){
        if(defined('PLUGIN_OPTIONS'))
            return $this->moduleShowTopic();
        return browser\msg::pageNotFound();
    }

  /*
   * show form for edite topic
   * @RETURN html content [title,body]
   */
    public function editeTopic(){
        if(defined('PLUGIN_OPTIONS'))
            return $this->moduleEditeTopic();
        return browser\msg::pageNotFound();
    }

    /*
   * show form for delete topic
   * @RETURN html content [title,body]
   */
    public function SureDeleteTopic(){
        if(defined('PLUGIN_OPTIONS'))
            return $this->moduleSureDeleteTopic();
        return browser\msg::pageNotFound();
    }

    /*
     * show form for delete replay
     * @RETURN html content [title,body]
     */
    public function sureDeleteReplay(){
        if(defined('PLUGIN_OPTIONS'))
            return $this->modulesureDeleteReplay();
        return browser\msg::pageNotFound();
    }

    /*
     * show Forum topics
     * @RETURN html content [title,body]
     */
    public function showForum(){
        if(defined('PLUGIN_OPTIONS'))
            return $this->moduleShowForum();
        return browser\msg::pageNotFound();
    }

    /*
     * EDITE REPLAY
     * @RETURN html content [title,body]
     */
    public function editeReplay(){
        if(defined('PLUGIN_OPTIONS'))
            return $this->moduleEditeReplay();
        return browser\msg::pageNotFound();
    }

    /*
     * show settings page
     * @RETURN html content [title,body]
     */
    public function settings(){
        if($this->hasAdminPanel())
            return $this->moduleSettings();
        return browser\msg::pageAccessDenied();
    }



	
}
