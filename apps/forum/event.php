<?php
namespace apps\forum;
use \Mega\cls\browser as browser;
use \Mega\cls\core as core;
use \Mega\cls\database as db;

class event extends module{
	use \Mega\Apps\users\addons;
	
	/*
	 * Edite or insert forum
	 * @param array $e, form properties
	 * @return array, form properties
	 */
	public function btnOnclickDoForum($e){
		if($this->isLogedin() && $this->hasAdminPanel() ){
			if($e['txtName']['VALUE'] == '')
				return browser\msg::modalNotComplete($e);
			$orm = db\orm::singleton();
			$forum = $orm->dispense('forums_forums');
			if(array_key_exists('hidID',$e))
				$forum = $orm->load('forums_forums',$e['hidID']['VALUE']);
			$forum->name = $e['txtName']['VALUE'];
			$forum->des = $e['txtDes']['VALUE'];
			$forum->localize = $e['cobLang']['SELECTED'];
            $forum->rank = $e['cobRank']['SELECTED'];
			$orm->store($forum);
			return browser\msg::modalSuccessfull($e,['service','administrator','load','forum','listForums']);
		}
		return browser\msg::modalNoPermission($e);
	}

    /*
    * delete catalogue
    * @param array $e, form properties
    * @return array, form properties
    */
    public function btnOnclickDeleteforum($e){
        if($this->isLogedin() && $this->hasAdminPanel() ){
            $orm = db\orm::singleton();
            $orm->exec('DELETE FROM forums_forums WHERE id=?;',[$e['hidID']['VALUE']],NON_SELECT);
            return browser\msg::modalSuccessfull($e,['service','administrator','load','forum','listForums']);
        }
        return browser\msg::modalNoPermission($e);
    }

    /*
    * delete catalogue
    * @param array $e, form properties
    * @return array, form properties
    */
    public function btnOnclickDoTopic($e){
        if($this->isLogedin()){
            $orm = db\orm::singleton();
            if($e['txtTitle']['VALUE'] == '')
                return browser\msg::modalNotComplete($e);
            elseif(strlen($e['txtBody']['VALUE']) < 12)
                return browser\msg::modal($e,_t('Message'),_t('body of topic most be more 10 characters.'),'warning');
            elseif(array_key_exists('hidForum',$e)) {
                if ($orm->count('forums_forums', 'id=?', [$e['hidForum']['VALUE']]) != 0) {
                    $topic = $orm->dispense('forums_topics');
                    $topic->title = $e['txtTitle']['VALUE'];
                    $topic->body = $e['txtBody']['VALUE'];
                    $topic->username = $this->getCurrentUserInfo()->id;
                    $topic->publishDate = time();
                    $topic->forum = $e['hidForum']['VALUE'];
                    $orm->store($topic);
                    return browser\msg::modalSuccessfull($e, ['forum', 'showTopic', $orm->store($topic)]);
                }
            }
        }
        return browser\msg::modalNoPermission($e);
    }

    /*
    * delete catalogue
    * @param array $e, form properties
    * @return array, form properties
    */
    public function btnOnclickUpdateTopic($e){
        if($this->isLogedin()){

            if($e['txtTitle']['VALUE'] == '')
                return browser\msg::modalNotComplete($e);
            elseif(strlen($e['txtBody']['VALUE']) < 12)
                return browser\msg::modal($e,_t('Message'),_t('body of topic most be more 10 characters.'),'warning');
            elseif(array_key_exists('hidForum',$e) && array_key_exists('hidTopic',$e)) {
                $orm = db\orm::singleton();
                if ($orm->count('forums_forums', 'id=?', [$e['hidForum']['VALUE']]) != 0 && $orm->count('forums_topics', 'id=?', [$e['hidTopic']['VALUE']]) != 0) {
                    $topic = $orm->load('forums_topics',$e['hidTopic']['VALUE']);
                    $user = $this->getCurrentUserInfo();
                    if(!is_null($user))
                        if($user->id == $topic->username){
                            $topic->title = $e['txtTitle']['VALUE'];
                            $topic->body = $e['txtBody']['VALUE'];
                            $orm->store($topic);
                            return browser\msg::modalSuccessfull($e, ['forum', 'showTopic', $e['hidTopic']['VALUE']]);
                        }
                }
            }
        }
        return browser\msg::modalNoPermission($e);
    }

    /*
   * delete topic
   * @param array $e, form properties
   * @return array, form properties
   */
    public function btnOnclickDeleteTopic($e){
        if($this->isLogedin()){
            if(array_key_exists('hidID',$e)){
                $orm = db\orm::singleton();
                if ($orm->count('forums_topics', 'id=?', [$e['hidID']['VALUE']]) != 0) {
                    $topic = $orm->load('forums_topics',$e['hidID']['VALUE']);
                    $user = $this->getCurrentUserInfo();
                    if(!is_null($user))
                        if($user->id == $topic->username){
                            //DELETE ALL REPLAYS AND TOPIC
                            $orm->exec('DELETE FROM forums_topics WHERE id=?;',[$topic->id],NON_SELECT);
                            $orm->exec('DELETE FROM forums_replays WHERE topic=?',[$topic->id],NON_SELECT);
                            return browser\msg::modalSuccessfull($e, ['forum', 'showTopic', $e['hidID']['VALUE']]);
                        }
                }
            }
        }
        return browser\msg::modalNoPermission($e);
    }

    /*
     * delete topic
     * @param array $e, form properties
     * @return array, form properties
     */
    public function btnOnclickDeleteReplay($e){
        if($this->isLogedin()){
            if(array_key_exists('hidID',$e)){
                $orm = db\orm::singleton();
                if ($orm->count('forums_replays', 'id=?', [$e['hidID']['VALUE']]) != 0) {
                    $replay = $orm->load('forums_replays',$e['hidID']['VALUE']);
                    $user = $this->getCurrentUserInfo();
                    if(!is_null($user))
                        if($user->id == $replay->username){
                            //DELETE ALL REPLAYS AND TOPIC
                            $orm->exec('DELETE FROM forums_replays WHERE id=?',[$replay->id],NON_SELECT);
                            return browser\msg::modalSuccessfull($e, ['forum', 'showTopic', $e['hidTopic']['VALUE']]);
                        }
                }
            }
        }
        return browser\msg::modalNoPermission($e);
    }

    /*
  * delete topic
  * @param array $e, form properties
  * @return array, form properties
  */
    public function btnOnclickSubmitReplay($e){
        if($this->isLogedin()){
            if(array_key_exists('hidID',$e)){
                $orm = db\orm::singleton();
                if($e['txtBody']['VALUE'] == '' || $e['txtBody']['VALUE'] == '<br>')
                    return browser\msg::modalNotComplete($e);
                elseif(strlen($e['txtBody']['VALUE']) < 10)
                    return browser\msg::modal($e,_t('Message'),_t('body of replay most be more 10 characters.'),'warning');
                elseif ($orm->count('forums_topics', 'id=?', [$e['hidID']['VALUE']]) != 0) {
                    $user = $this->getCurrentUserInfo();
                    $replay = $orm->dispense('forums_replays');
                    $replay->body = $e['txtBody']['VALUE'];
                    $replay->publishDate = time();
                    $replay->username = $user->id;
                    $replay->topic = $e['hidID']['VALUE'];
                    $orm->store($replay);
                    return browser\msg::modalSuccessfull($e,'R');
                }
            }
        }
        return browser\msg::modalNoPermission($e);
    }

    /*
* delete catalogue
* @param array $e, form properties
* @return array, form properties
*/
    public function btnOnclickUpdateReplay($e){
        if($this->isLogedin()){

            if($e['txtBody']['VALUE'] == '')
                return browser\msg::modalNotComplete($e);
            elseif(strlen($e['txtBody']['VALUE']) < 12)
                return browser\msg::modal($e,_t('Message'),_t('body of topic most be more 10 characters.'),'warning');
            elseif(array_key_exists('hidID',$e) ){
                $orm = db\orm::singleton();
                if ($orm->count('forums_replays', 'id=?', [$e['hidID']['VALUE']]) != 0) {
                    $replay = $orm->load('forums_replays',$e['hidID']['VALUE']);
                    $user = $this->getCurrentUserInfo();
                    if(!is_null($user))
                        if($user->id == $replay->username){
                            $replay->body = $e['txtBody']['VALUE'];
                            $orm->store($replay);
                            return browser\msg::modalSuccessfull($e, ['forum', 'showTopic', $replay->topic]);
                        }
                }
            }
        }
        return browser\msg::modalNoPermission($e);
    }

    /*
	 * Edite or insert forum
	 * @param array $e, form properties
	 * @return array, form properties
	 */
    public function btnOnclickSaveSettings($e){
        if($this->isLogedin() && $this->hasAdminPanel() ){
            if(\core\data\type::isNumber($e['postNumInHome']['SELECTED'])){
                $registry = core\registry::singleton();
                $registry->set('forum','postNumInHome',$e['postNumInHome']['SELECTED']);
                return browser\msg::modalSuccessfull($e);
            }
            return browser\msg::modalEventError($e);
        }
        return browser\msg::modalNoPermission($e);
    }
}
