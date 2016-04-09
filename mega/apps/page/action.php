<?php
namespace Mega\Apps\page;
use \Mega\Cls\core as core;
use \Mega\Cls\browser as browser;


class action extends module{
	use view;
	/*
	 * construct
	 */
	function __construct(){
		parent::__construct();
	}
	
	/*
	 * show page with id
	 * @RETURN html content [title,body]
	 */
	public function show(){
		return $this->moduleShow();
	}
	
	/*
	 * show list of catalogues
	 * @RETURN html content [title,body]
	 */
	public function catalogues(){
		if($this->isLogedin())
			return $this->moduleCatalogues();
		return core\router::jump(['service','users','login','service/administrator/load/page/catalogues']);
	}

    /*
	 * show form for add new catalogue
	 * @RETURN html content [title,body]
	 */
    public function newCat(){
        if($this->isLogedin())
            return $this->moduleNewCat();
        return core\router::jump(['service','users','login','service/administrator/load/page/newCat']);
    }
    
    /*
	 * show form for delete catalogue
	 * @RETURN html content [title,body]
	 */
    public function sureDeleteCat(){
        if($this->isLogedin())
            return $this->moduleSureDeleteCat();
        return core\router::jump(['service','users','login','service/administrator/load/page/catalogues']);
    }
    
    /*
	 * edite catalogue form
	 * @RETURN html content [title,body]
	 */
    public function editeCat(){
        if($this->isLogedin())
            return $this->moduleEditeCat();
        return core\router::jump(['service','users','login','service/administrator/load/page/catalogues']);
    }
    
    /*
     * show settings page 
     * @RETURN html content [title,body]
     */
    public function settings(){
		 if($this->isLogedin())
            return $this->moduleSettings();
        return core\router::jump(['service','users','login','service/administrator/load/page/settings']);
	}
	
	/*
	 * show form for post new page
	 * @RETURN html content [title,body]
	 */
    public function newPage(){
        if($this->isLogedin())
            return $this->moduleNewPage();
        return core\router::jump(['service','users','login','service/administrator/load/page/newPage']);
    }
    
    /*
	 * show list of all pages
	 * @RETURN html content [title,body]
	 */
    public function listPages(){
        if($this->isLogedin())
            return $this->moduleListPages();
        return core\router::jump(['service','users','login','service/administrator/load/page/listPages']);
    }
    
     /*
	 * show page for delete page
	 * @RETURN html content [title,body]
	 */
    public function sureDeletePage(){
        if($this->isLogedin())
            return $this->moduleSureDeletePage();
        return core\router::jump(['service','users','login','service/administrator/load/page/listPages']);
    }
    
    /*
	 * show form for edite post
	 * @RETURN html content [title,body]
	 */
    public function editePost(){
        if($this->isLogedin())
            return $this->moduleEditePost();
        return core\router::jump(['service','users','login','service/administrator/load/page/newPage']);
    }
    
    /*
	 * show pages in catalogue
	 * @return html content [title,body]
	 */
    public function catalogue(){
            return $this->moduleCatalogue();
    }

    /**
     * show list of comments
     * @return html content [title,body]
     */
    public function listComments(){
        if($this->isLogedin())
            return $this->moduleListComments();
        return core\router::jump(['service','users','login','service/administrator/load/page/listComments']);
    }
}
