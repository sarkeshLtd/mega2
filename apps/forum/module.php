<?php
namespace apps\forum;
use Mega\Cls\Browser as browser;
use Mega\Cls\Network as network;
use Mega\Cls\Core as core;
use Mega\cls\Database as db;

class module extends view{
	use addons;
	
	/*
	 * construct
	 */
	function __construct(){}
	
	//this function return back menus for use in admin area
	public static function coreMenu(){
		$menu = array();
		$url = core\general::createUrl(['service','administrator','load','forum','listForums']);
		array_push($menu,[$url, _('Manage forums')]);
		$url = core\general::createUrl(['service','administrator','load','forum','settings']);
		array_push($menu,[$url, _('Settings')]);

		$ret = [];
		array_push($ret, ['<span class="glyphicon glyphicon-comment" aria-hidden="true"></span>' , _('Forums')]);
		array_push($ret,$menu);
		return $ret;
	}
	
	/*
	 * show list of forums
	 * @RETURN html content [title,body]
	 */
	protected function moduleListForums(){
		if($this->hasAdminPanel()){
			$orm = db\orm::singleton();
			$forums = $orm->exec("SELECT f.id,f.name,f.des,l.language_name FROM forums_forums f INNER JOIN localize l WHERE l.id=f.localize ORDER BY f.rank;",[],SELECT);
			return $this->viewListForums($forums);
		}
		return browser\msg::pageAccessDenied();	
	}
	
	/*
	 * show form for add new forum
	 * @RETURN html content [title,body]
	 */
    protected function moduleNewForum(){
        if($this->hasAdminPanel()){
            $orm = db\orm::singleton();
            $localize = core\localize::singleton();
            return $this->viewNewDoForum($orm->findAll('localize'),$localize->localize());
        }
        return browser\msg::pageAccessDenied();
    }
    
    /*
	 * show form for add new forum
	 * @RETURN html content [title,body]
	 */
    protected function moduleEditeForum(){
        if($this->hasAdminPanel()){
			if(defined('PLUGIN_OPTIONS'))
				$options = explode('/',PLUGIN_OPTIONS);
				if(count($options == 3)){
					$orm = db\orm::singleton();
					if($orm->count('forums_forums','id=?',[$options[2]]) != 0){
						$localize = core\localize::singleton();
						return $this->viewNewDoForum($orm->findAll('localize'),$localize->localize(),$orm->load('forums_forums',$options[2]));
					}
				}
        }
        return browser\msg::pageAccessDenied();
    }

    /*
	 * show form for delete forum
	 * @RETURN html content [title,body]
	 */
    protected function moduleSureDeleteforum(){
        if($this->hasAdminPanel()){
            if(defined('PLUGIN_OPTIONS'))
                $options = explode('/',PLUGIN_OPTIONS);
            if(count($options == 3)){
                $orm = db\orm::singleton();
                if($orm->count('forums_forums','id=?',[$options[2]]) != 0){
                    return $this->viewSureDeletForum($orm->load('forums_forums',$options[2]));
                }
            }
        }
        return browser\msg::pageAccessDenied();
    }

    /*
	 * Post new topic
	 * @RETURN html content [title,body]
	 */
    protected function moduleNewTopic(){
        if(defined('PLUGIN_OPTIONS')){
            $orm = db\orm::singleton();
            if($orm->count('forums_forums','id=?',[PLUGIN_OPTIONS]) != 0){
                return $this->viewNewTopic($orm->load('forums_forums',PLUGIN_OPTIONS));
            }
        }
        return browser\msg::pageNotFound();
    }

    /*
	 * show main page of forum
	 * @RETURN html content [title,body]
	 */
    protected function moduleMain(){
        $orm = db\orm::singleton();
        $forums = $orm->exec('SELECT * FROM forums_forums ORDER BY rank',[],SELECT);
        $forumsInfo = [];
        $registry = core\registry::singleton();
        $postNum = $registry->get('forum','postNumInHome');
        $queryString = 'SELECT ft.id,ft.title,ft.body,u.username FROM forums_topics ft INNER JOIN users u ON u.id = ft.username WHERE ft.forum=? ORDER BY ft.publishDate DESC LIMIT ?;';
        foreach($forums as $key=>$forum){
            $objInfo = new \Mega\Cls\Data\obj;
            $objInfo->forumInfo = $forum;
            $objInfo->lastTopics = $orm->exec($queryString,[$forum->id,$postNum],SELECT);
            array_push($forumsInfo,$objInfo);
        }
        return $this->viewMain($forumsInfo);
    }

    /*
    * show topic
    * @RETURN html content [title,body]
    */
    protected function moduleShowTopic(){
        $orm = db\orm::singleton();
        if($orm->count('forums_topics','id=?',[PLUGIN_OPTIONS]) != 0){
            $topic = $orm->exec('SELECT ft.id,ft.forum,ft.title,ft.body,ft.publishDate,u.username,u.photo FROM forums_topics ft INNER JOIN users u ON u.id=ft.username WHERE ft.id=?;',[PLUGIN_OPTIONS],SELECT_ONE_ROW);
            $replays = $orm->exec('SELECT fr.id,fr.body,fr.publishDate,u.username,u.photo FROM forums_replays fr INNER JOIN users u ON u.id=fr.username WHERE fr.topic=? ORDER BY fr.publishDate;',[PLUGIN_OPTIONS],SELECT);
            $forum = $orm->load('forums_forums',$topic->forum);
            $userInfo = $this->getCurrentUserInfo();
            return $this->viewShowTopic($forum,$topic,$replays,$userInfo);

        }
        return browser\msg::pageNotFound();
    }

    /*
     * show form for edite topic
     * @RETURN html content [title,body]
     */
    protected function moduleEditeTopic(){
        if($this->isLogedin()){
            if(defined('PLUGIN_OPTIONS')){
                $orm = db\orm::singleton();
                if($orm->count('forums_topics','id=?',[PLUGIN_OPTIONS]) != 0){
                    $topic = $orm->load('forums_topics',PLUGIN_OPTIONS);
                    $user = $this->getCurrentUserInfo();
                    if(!is_null($user))
                        if($user->id == $topic->username){
                            $forum = $orm->load('forums_forums',$topic->forum);
                            return $this->viewEditeTopic($forum,$topic);
                        }
                }
            }
            return browser\msg::pageNotFound();
        }
        return browser\msg::pageAccessDenied();
    }

    /*
   * show form for delete topic
   * @RETURN html content [title,body]
   */
    protected function moduleSureDeleteTopic(){
        if($this->isLogedin()){
            if(defined('PLUGIN_OPTIONS')){
                $orm = db\orm::singleton();
                if($orm->count('forums_topics','id=?',[PLUGIN_OPTIONS]) != 0){
                    $topic = $orm->load('forums_topics',PLUGIN_OPTIONS);
                    $user = $this->getCurrentUserInfo();
                    if(!is_null($user))
                        if($user->id == $topic->username){
                            $forum = $orm->load('forums_forums',$topic->forum);
                            return $this->viewSureDeleteTopic($forum,$topic);
                        }
                }
            }
            return browser\msg::pageNotFound();
        }
        return browser\msg::pageAccessDenied();
    }


    /*
    * show form for delete replay
    * @RETURN html content [title,body]
    */
    protected function moduleSureDeleteReplay(){
        if($this->isLogedin()){
            if(defined('PLUGIN_OPTIONS')){
                $orm = db\orm::singleton();
                if($orm->count('forums_replays','id=?',[PLUGIN_OPTIONS]) != 0){
                    $replay = $orm->load('forums_replays',PLUGIN_OPTIONS);
                    $topic = $orm->load('forums_topics',$replay->topic);
                    $user = $this->getCurrentUserInfo();
                    if(!is_null($user))
                        if($user->id == $replay->username){
                            return $this->viewSureDeleteReplay($topic,$replay);
                        }
                }
            }
            return browser\msg::pageNotFound();
        }
        return browser\msg::pageAccessDenied();
    }

    /*
     * show forum topics
     * @RETURN html content [title,body]
     */
    protected function moduleShowForum(){
        $orm = db\orm::singleton();
        if($orm->count('forums_forums','id=?',[PLUGIN_OPTIONS]) != 0){
            $topic = $orm->exec('SELECT ft.id,ft.forum,ft.title,ft.body,ft.publishDate,u.username,u.photo FROM forums_topics ft INNER JOIN users u ON u.id=ft.username WHERE ft.forum=? ORDER BY ft.publishDate DESC;',[PLUGIN_OPTIONS],SELECT);
            $forum = $orm->load('forums_forums',PLUGIN_OPTIONS);
            return $this->viewShowForum($forum,$topic);

        }
        return browser\msg::pageNotFound();
    }

    /*
     * EDITE REPLAY
     * @RETURN html content [title,body]
     */
    protected function moduleEditeReplay(){
        $orm = db\orm::singleton();
        if($orm->count('forums_replays','id=?',[PLUGIN_OPTIONS]) != 0){
            $user = $this->getCurrentUserInfo();
            $replay = $orm->findOne('forums_replays','id=?',[PLUGIN_OPTIONS]);
            if(!is_null($user))
                if($user->id == $replay->username)
                    return $this->viewEditeReplay($replay);
        }
        return browser\msg::pageNotFound();
    }

    /*
    * show last replays in forum
    * @return array [title,content]
    */
    protected function moduleLastReplays(){
        $orm = db\orm::singleton();
        $registry = core\registry::singleton();
        $replays = $orm->exec('SELECT  ft.id,ft.title,u.username FROM forums_replays fr INNER JOIN users u ON u.id=fr.username INNER JOIN forums_topics ft ON ft.id=fr.topic  ORDER BY fr.publishDate DESC LIMIT ?;',[$registry->get('forum','widgetNumReplays')],SELECT);
        return $this->viewLastReplays($replays);
    }

    /**
     * show last topic that created
     * @return array [title,content]
     */
    public function moduleLastTopics(){
        $orm = db\orm::singleton();
        $registry = core\registry::singleton();
        $topics = $orm->exec('SELECT  ft.id,ft.title,u.username FROM forums_topics ft INNER JOIN users u ON ft.username = u.id  ORDER BY ft.publishDate DESC LIMIT ?;',[$registry->get('forum','widgetNumTopics')],SELECT);
        return $this->viewLastTopics($topics);
    }

    /*
     * show settings page
     * @RETURN html content [title,body]
     */
    protected function moduleSettings(){
        $registry = core\registry::singleton();
        return $this->viewSettings($registry->getPlugin('forum'));
    }
}
