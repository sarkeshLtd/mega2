<?php
namespace Mega\Apps\users;
use Mega\cls\browser as browser;
use Mega\cls\network as network;
use Mega\cls\core as core;
use Mega\Cls\Database as db;

class module{
	use view;
	use addons;
	use \Mega\Apps\files\addons;
	
	/*
	 * construct
	 */
	function __construct(){}
	
	//this function return back menus for use in admin area
	public static function coreMenu(){
		$menu = array();
		$url = core\general::createUrl(['service','administrator','load','users','listPeople']);
		array_push($menu,[$url, _('People')]);
		$url = core\general::createUrl(['service','administrator','load','users','listGroups']);
		array_push($menu,[$url, _('Groups')]);
		$url = core\general::createUrl(['service','administrator','load','users','accountSettings']);
		array_push($menu,[$url, _('Account settings')]);
		$url = core\general::createUrl(['service','administrator','load','users','ipBlockList']);
		array_push($menu,[$url, _('IP address blocking')]);
		$ret = array();
		array_push($ret, ['<span class="glyphicon glyphicon-user" aria-hidden="true"></span>' , _('Users')]);
		array_push($ret,$menu);
		return $ret;
	}
	
	/*
	 * show login form
	 * @return string, html content(logedin :null)
	 */
	protected function moduleFrmLogin($position){
		if(!$this->isLogedin())
			return $this->frmLogin($position);
		elseif($this->isLogedin() && $position == 'content')
			return core\router::jump(['users','profile']);
		return null;
	}
	
	/*
	 * user login proccess
	 * @param array $e, form properties
	 * @return array, form properties
	 */
	protected function onclickLogin($e){
		$orm = db\orm::singleton();
		$count = $orm->count('users',"(username = ? or email=?) and password = ?", array($e['username']['VALUE'],$e['username']['VALUE'],md5($e['password']['VALUE'])));
		if($count != 0){
			//login data is cerrect
			$validator = new network\validator;
			if($e['ckbRemember']['CHECKED'] == '1') $validID = $validator->set('USERS_LOGIN',true);
			else $validID = $validator->set('USERS_LOGIN',false);

			//INSERT VALID ID IN USER ROW
			$user = $orm->load('users',$this->getUserID($e['username']['VALUE']));
			$user->login_key = $validID;
			$user->last_login = time();
			$orm->store($user);
			if(array_key_exists('hidJump',$e))
				$e['RV']['URL'] = core\general::createUrl([$e['hidJump']['VALUE']]);
			else
				$e['RV']['URL'] = 'R';
		}
		else{
			//username or password is incerrect
			$e['username']['VALUE'] = '';
			$e['password']['VALUE'] = '';
			$e['RV']['MODAL'] = browser\page::showBlock(_('Message'), _('Username or Password is incerrect!'), 'MODAL','type-warning');
		}
		return $e;
	}
	
	/*
	 * show minimal profile in widget mode
	 * @return string, html content
	 */
	protected function moduleWidgetProfile(){
		if($this->isLogedin()){
			//get user info
			$orm = db\orm::singleton();
			$user = $this->getCurrentUserInfo();
			
			if(!is_null($user))
				return $this->viewWidgetProfile($user,$this->hasPermission('adminPanel'));
		}
		return null;
	}
	
	/*
	 * show register form
	 * @return string, html content
	 */
	protected function moduleFrmRegister(){
		if(!$this->isLogedin()){
			$registry = core\registry::singleton();
			if($registry->get('users','register') == '1')
				return $this->viewFrmRegister();
			return browser\msg::pageNotFound();
		}
		return core\router::jump(['users','profile']);
	}
	
	/*
	 * show active form or active user
	 * @return string, html content
	 */
	protected function moduleActiveAccount(){
		if(PLUGIN_OPTIONS == '')
			return browser\msg::pageNotFound();
		//going to active user account
		$orm = db\orm::singleton();
		$validator = new network\validator;
		if($validator->checkSid(PLUGIN_OPTIONS) && $orm->count('users','state=?',['A:'. PLUGIN_OPTIONS]) != 0){
			ECHO 555;
			$user = $orm->findOne('users','state=?',['A:'. PLUGIN_OPTIONS]);
			$registry = core\registry::singleton();
			$settings = $registry->getPlugin('users');
			$user->permission = $settings->defaultPermission;
			$user->state = 'E';
			$orm->store($user);
			//login user to system
			$this->loginWithUsername($user->username);
			//jump to change password form
			return core\router::jump(['users','changePassword','newUser']);
		}
		//show fail message
		return $this->viewFailActiveAccount();
	}
	
	/*
	 * show login form in single page
	 * @return string html content
	 */
	protected function moduleLoginSinglePage(){
		$loginForm = $this->frmLogin();
		$page = browser\page::simplePage($loginForm[0],browser\page::showBlock($loginForm[0],$loginForm[1],'BLOCK'),5,true);
		return $page;
	}
	
	/*
	 * show list of blocked ips
	 * @return string, html content
	 */
	protected function moduleIpBlockList(){
		if($this->hasAdminPanel()){
			$orm = db\orm::singleton();
			return $this->viewIpBlockList($orm->getAll('ipblock'));
		}
		return browser\msg::pageAccessDenied();
	}
	
	/*
	 * add new ip to block list
	 * @return string, html content
	 */
	protected function moduleNewIpBlock(){
		if($this->hasAdminPanel()){
			return $this->viewNewIpBlock();
		}
		return browser\msg::pageAccessDenied();
	}
	
	/*
	 * reset code defined proccess to define new password
	 * @return string, html content
	 */
	protected function moduleResetPassword(){
		$validator = new network\validator;
		if($validator->checkSid(PLUGIN_OPTIONS)){
			$orm = db\orm::singleton();
			if($orm->count('users','forget=?',[PLUGIN_OPTIONS]) != 0){
				$user = $orm->findOne('users','forget=?',[PLUGIN_OPTIONS]);
				$user->forget = '';
				$orm->store($user);
				$this->loginWithUsername($user->username);
				return core\router::jump(['users','changePassword']);
			}
		}
		return $this->viewResetPasswordExpire();
	}
	
	/*
	 * show user profile
	 * @return string, html content
	 */
	protected function moduleProfile(){
		if(!defined('PLUGIN_OPTIONS')){
			//SHOW USER OWN PROFILE
			if($this->isLogedin()){
				$user = $this->getCurrentUserInfo();
				if(is_null($user->photo))
					$user->photo = DOMAIN_EXE . '/plugins/system/users/images/def_avatar_128.png';
				else
					$user->photo = $this->getFileAddress($user->photo);
				return $this->viewOwnProfile($user);
			}
			return core\router::jump(['users','login']);
		}
	}
	/*
	 * show page to user for select new or change avatar
	 * @return array, [title,body]
	 */
	protected function moduleChangeAvatar(){
		$registry = core\registry::singleton();
		$settings = $registry->getPlugin('users');
		if($settings->usersCanUploadAvatar == 1){
			return $this->viewChangeAvatar($settings);
		}
		return browser\msg::pageAccessDenied();
	}
	
	/*
	 * show list of people in administrator area
	 * @return array, [title,body]
	 */
	protected function moduleListPeople(){
		if($this->hasAdminPanel()){
			$orm = db\orm::singleton();
			return $this->viewListPeople($orm->findAll('users'),$orm->findAll('permissions'));
		}
		return browser\msg::pageAccessDenied();
	}
	
	/*
	 * SHOW USER PLUGIN SETTINGS
	 * @return array, [title,body]
	 */
	protected function moduleAccountSettings(){
		if($this->hasAdminPanel()){
			$orm = db\orm::singleton();
			$registry = core\registry::singleton();
			$settings = $registry->getPlugin('users');
			return $this->viewAccountSettings($settings,$orm->find('permissions','name <> ?;',['Guest']));
		}
		return browser\msg::pageAccessDenied();
	}
	
	/*
	 * edite user information
	 * @return array, [title,body]
	 */
	protected function moduleEditeUser(){
		if($this->hasAdminPanel()){
			$options = explode('/',PLUGIN_OPTIONS);
			if(count($options == 3)){
				$orm = db\orm::singleton();
				if($orm->count('users','id=?',[$options[2]]) != 0){
					$registry = core\registry::singleton();
					return $this->viewEditeUser($orm->load('users',$options[2]), $orm->find('permissions','name <> ?;',['Guest']),$registry->getPlugin('users'));
				}
			}
			return browser\msg::pageNotFound();
		}
		return browser\msg::pageAccessDenied();
	}
	
	/*
	 * show list of groups
	 * @return array, [title,body]
	 */
	protected function moduleListGroups(){
		if($this->hasAdminPanel()){
			$orm = db\orm::singleton();
			return $this->viewListGroups($orm->findAll('permissions'));
		}
		return browser\msg::pageAccessDenied();
	}
	
	/*
	 * show form for add new group
	 * @return array, [title,body]
	 */
	protected function moduleNewGroup(){
		if($this->hasAdminPanel())
			return $this->viewNewGroup();
		return browser\msg::pageAccessDenied();
	}

    /*
	 * show form edite group
	 * @return array, [title,body]
	 */
    protected function moduleEditeGroup(){
        if($this->hasAdminPanel()){
            //get group data
            $options = explode('/',PLUGIN_OPTIONS);
            if(count($options) == 3){
                $orm = db\orm::singleton();
                if($orm->count('permissions','id=?',[$options[2]]) != 0){
                    $group = $orm->load('permissions',$options[2]);
                    return $this->viewEditeGroup($group);
                }
                return browser\msg::pageNotFound();
            }
            return browser\msg::pageError();
        }
            return $this->viewNewGroup();
        return browser\msg::pageAccessDenied();
    }
    
    /*
	 * webservice for login user to system
	 * @param string $username
	 * @param string $password
	 * @return string (0=incerrect username or password, else:string is special login key)
	 */
	protected function moduleWsLogin($username,$password){
		$orm = db\orm::singleton();
		$count = $orm->count('users',"(username = ? or email=?) and password = ?", [$username,$username,md5($password)]);
		if($count != 0){
			//login data is cerrect
			$validator = new network\validator;
			$validID = $validator->set('USERS_LOGIN',true);
		
			//INSERT VALID ID IN USER ROW
			$user = $orm->load('users',$this->getUserID($username));
			$user->login_key = $validID;
			$user->last_login = time();
			$orm->store($user);
			return $validID;
		}
		return 0;
	}
	
	/*
	 * FUNCTION FOR CHECK USER SPECIAL KEY
	 * @return integer(1= is loged in,2=not valid)
	 */
	public function moduleWsCheckLogin(){
		$orm = db\orm::singleton();
		if($orm->count('users','login_key=?',[PLUGIN_OPTIONS]) != 0)
			return 1;
		return 0;
	}
}
