<?php
namespace Mega\Apps\page;
use Mega\Cls\browser as browser;
use Mega\Cls\network as network;
use Mega\Cls\core as core;
use Mega\Cls\page as page;
use Mega\Cls\Database as db;

class module{
	use view;
	use \Mega\Apps\menus\addons;
	
	/*
	 * construct
	 */
	function __construct(){}
	
	//this function return back menus for use in admin area
	public static function coreMenu(){
		$menu = array();
		$url = core\general::createUrl(['service','administrator','load','page','newPage']);
		array_push($menu,[$url, _('New Page')]);
		$url = core\general::createUrl(['service','administrator','load','page','listPages']);
		array_push($menu,[$url, _('List Pages')]);
		$url = core\general::createUrl(['service','administrator','load','page','catalogues']);
		array_push($menu,[$url, _('Catalogues')]);
        $url = core\general::createUrl(['service','administrator','load','page','listComments']);
        array_push($menu,[$url, _('Comments')]);
		$url = core\general::createUrl(['service','administrator','load','page','settings']);
		array_push($menu,[$url, _('Page Settings')]);

		$ret = [];
		array_push($ret, ['<span class="glyphicon glyphicon-book" aria-hidden="true"></span>' , _('Pages')]);
		array_push($ret,$menu);
		return $ret;
	}
	
	/*
	 * show page with id
	 * @RETURN html content [title,body]
	 */
	protected function moduleShow(){	
		if(defined('PLUGIN_OPTIONS')){
			$orm = db\orm::singleton();
			if($orm->count('page_posts','adr=?',[PLUGIN_OPTIONS]) != 0){
				$post = $orm->exec('SELECT p.id,u.username,p.tags,p.photo,p.title,p.body,p.date FROM page_posts p INNER JOIN users u ON u.id = p.username WHERE p.adr=?',[PLUGIN_OPTIONS],SELECT_ONE_ROW);
                $comments = $orm->exec('SELECT pc.date,u.username,u.photo,pc.body FROM page_comments pc INNER JOIN users u ON u.id = pc.username WHERE pc.post=?',[$post->id],SELECT);
                $registry = core\registry::singleton();
				return $this->viewShow($post,$comments,$registry->getPlugin('page'));
			}
		}
		return browser\msg::pageNotFound();
	}
	
	/*
	 * show list of catalogues
	 * @RETURN html content [title,body]
	 */
	protected function moduleCatalogues(){
		if($this->hasAdminPanel()){
			$orm = db\orm::singleton();
			$cats = $orm->exec("SELECT c.id,l.language_name,c.name,c.canComment,c.adr FROM page_catalogue c INNER JOIN localize l ON c.localize = l.id;",[],SELECT);
			return $this->viewCatalogues($cats);
		}
		return browser\msg::pageAccessDenied();	
	}

    /*
	 * show form for add new catalogue
	 * @RETURN html content [title,body]
	 */
    protected function moduleNewCat(){
        if($this->hasAdminPanel()){
            $orm = db\orm::singleton();
            $localize = core\localize::singleton();
            return $this->viewNewCat($orm->findAll('localize'),$localize->localize());
        }
        return browser\msg::pageAccessDenied();
    }
    
    /*
	 * show form for delete catalogue
	 * @RETURN html content [title,body]
	 */
    protected function moduleSureDeleteCat(){
		if($this->hasAdminPanel()){
			$options = explode('/',PLUGIN_OPTIONS);
			if(count($options == 3)){
				$orm = db\orm::singleton();
				if($orm->count('page_catalogue','id=?',[$options[2]]) != 0)
					return $this->viewSureDeletCat($orm->load('page_catalogue',$options[2]));
			}
			return browser\msg::pageNotFound();
		}
		return browser\msg::pageAccessDenied();
    }
    
    /*
	 * edite catalogue form
	 * @RETURN html content [title,body]
	 */
    protected function moduleEditeCat(){
        if($this->hasAdminPanel()){
			$options = explode('/',PLUGIN_OPTIONS);
			if(count($options == 3)){
				$orm = db\orm::singleton();
				if($orm->count('page_catalogue','id=?',[$options[2]]) != 0){
					$localize = core\localize::singleton();
					return $this->viewEditeCat($orm->load('page_catalogue',$options[2]),$orm->findAll('localize'),$localize->localize());
				}
			}
        }
        return browser\msg::pageAccessDenied();
    }
    
    /*
     * show settings page 
     * @RETURN html content [title,body]
     */
    protected function moduleSettings(){
		if($this->hasAdminPanel()){
			$orm = db\orm::singleton();
			$registry = core\registry::singleton();
			return $this->viewSettings($registry->getPlugin('page'));
			
        }
        return browser\msg::pageAccessDenied();
	}
	
	/*
	 * show form for post new page
	 * @RETURN html content [title,body]
	 */
    protected function moduleNewPage(){
        if($this->hasAdminPanel()){
			$orm = db\orm::singleton();
			$registry = core\registry::singleton();
			if($orm->count('page_catalogue') != 0)
				return $this->viewDoPage($registry->getPlugin('page'),$orm->findAll('page_catalogue'));
			//user first should create catalogue
			return $this->viewMsgAddCatalogue();
			
        }
        return browser\msg::pageAccessDenied();
    }
    
    /*
	 * show list of pages
	 * @RETURN html content [title,body]
	 */
    protected function moduleListPages(){
        if($this->hasAdminPanel()){
			//check page
			if(defined('PLUGIN_OPTIONS')){
				$options = explode('/',PLUGIN_OPTIONS);
			}
			$pageNum = 0;
			if(count($options) > 2)
				$pageNum = $options[2];
			$registry = core\registry::singleton();
			$postPerPage = $registry->get('page','PostPerPage');
			$startFrom = ($pageNum * $postPerPage);
			$orm = db\orm::singleton();
			$hasPre = true;
			if($startFrom == 0) $hasPre = false;
			$count = $orm->count('page_posts');
			$hasNext = true;
			if(($startFrom + $postPerPage) >= $count) $hasNext = false;
			$pages = $orm->exec('SELECT p.id,p.title,p.adr,c.name FROM page_posts p INNER JOIN page_catalogue c ON p.catalogue=c.id ORDER BY p.date DESC limit 5 OFFSET ?;',[$startFrom],SELECT);
			return $this->viewListPages($pages,$hasPre,$hasNext,$pageNum);
			
        }
        return browser\msg::pageAccessDenied();
    }
    
     /*
	 * show page for delete page
	 * @RETURN html content [title,body]
	 */
    protected function moduleSureDeletePage(){
        if($this->hasAdminPanel()){
			//check page
			if(defined('PLUGIN_OPTIONS')){
				$options = explode('/',PLUGIN_OPTIONS);
				if(count($options) == 3){
					$orm = db\orm::singleton();
					if($orm->count('page_posts','id=?',[$options[2]]) != 0)
						return $this->viewSureDeletPost($orm->load('page_posts',$options[2]));
				}
			}
        }
        return browser\msg::pageAccessDenied();
    }
    
    /*
	 * show form for edite post
	 * @RETURN html content [title,body]
	 */
    protected function moduleEditePost(){
       if($this->hasAdminPanel()){
		   if(defined('PLUGIN_OPTIONS')){
				$options = explode('/',PLUGIN_OPTIONS);
				if(count($options) == 3){
					$orm = db\orm::singleton();
					$registry = core\registry::singleton();
					if($orm->count('page_catalogue') != 0 )
						return $this->viewDoPage($registry->getPlugin('page'),$orm->findAll('page_catalogue'),$orm->load('page_posts',$options[2]));
					//user first should create catalogue
					return $this->viewMsgAddCatalogue();
				}
			}
		   
			$orm = db\orm::singleton();
			$registry = core\registry::singleton();
			if($orm->count('page_catalogue') != 0)
				return $this->viewDoPage($registry->getPlugin('page'),$orm->findAll('page_catalogue'));
			
        }
        return browser\msg::pageAccessDenied();
    }
    
    /*
	 * show list of catalogue in widget mode
	 * @return array [title,body]
	 */
	protected function moduleWidgetCatalogues(){
		//get catalogues
		$localize = core\localize::singleton();
		$orm = db\orm::singleton();
		$local = $localize->localize();
		$cats = $orm->find('page_catalogue','localize = ?',[$local->id]);
		$ccats = [];
		foreach($cats as $cat){
			array_push($ccats, ['label' => $cat->name,'url' => core\general::createUrl(array('page','catalogue',$cat->id)	)]);
		}
		return [_('Blog catalogues'),$this->createMenu($ccats,0,FALSE)];
	}
	
	/*
	 * show pages in catalogue
	 * @RETURN html content [title,body]
	 */
    protected function moduleCatalogue(){
		$orm = db\orm::singleton();
		if(defined('PLUGIN_OPTIONS')){
            $options = explode('/',PLUGIN_OPTIONS);
            $page = 1;
            if(count($options) == 2)
                $page = $options['1'];
			if($orm->count('page_catalogue','id=?',[$options['0']]) != 0 ){
                $registry = core\registry::singleton();
				$cat = $orm->findOne('page_catalogue','id=?',[$options['0']]);
                //find pages
                $sql = 'SELECT * FROM page_posts WHERE catalogue=? ORDER BY date DESC;';
                $params = [$cat->id];
                $pagePerPage = $registry->get('page','PostPerPage');
                $pages = page\paging::getPageContent($sql,$params,$page,$pagePerPage);
                $hasNext = page\paging::hasNext('page_posts',$page,$pagePerPage);
                $hasPre = page\paging::hasPre('page_posts',$page,$pagePerPage);
				return $this->viewShowCtatlogePages($pages,$cat,$hasNext,$hasPre,$page);
			}
		}
    }

    /**
     * show list of comments
     * @return html content [title,body]
     */
    protected function moduleListComments(){
        if($this->hasAdminPanel()){
            $options = explode('/',PLUGIN_OPTIONS);
            $page = 1;
            if(count($options) == 3)
                $page = $options[2];
            $sql = 'SELECT p.id,u.username,p.body FROM page_comments p INNER JOIN users u ON u.id = p.username ORDER BY p.date DESC';
            $hasNext = page\paging::hasNext('page_comments',$page,5);
            $hasPre = page\paging::hasPre('page_comments',$page,5);
            return $this->viewListComments(page\paging::getPageContent($sql,[],$page,5),$hasNext,$hasPre,$page);
        }
        return browser\msg::pageAccessDenied();
    }

    /**
     * show last replays on pages
     * @return array [title,body]
     */
    public function moduleLastReplays(){
        $orm = db\orm::singleton();
        $replays = $orm->exec('SELECT  p.adr,p.title,u.username FROM page_posts p INNER JOIN page_comments c ON c.post = p.id INNER JOIN users u ON u.id=c.username  ORDER BY p.date DESC LIMIT 5;',[],SELECT);
        if(!is_null($replays)){
            $rreplays = [];
            foreach($replays as $replay){
                array_push($rreplays, ['label' => sprintf(_('%s in %s'),$replay->username,$replay->title) ,'url' => core\general::createUrl(array('page','show',$replay->adr)	)]);
            }
            return [_('Last comments'),$this->createMenu($rreplays,0,FALSE)];
        }
        return [null,null];
    }
}
