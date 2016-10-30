<?php
namespace Mega\Apps\users;
use \Mega\cls\browser as browser;
use \Mega\cls\network as network;
use \Mega\cls\core as core;
use \Mega\Cls\Database as db;
use \Mega\data as data;

class event extends module{
	use \Mega\Apps\Administrator\addons;
	
	private $settings;
	
	function __construct(){
		$registry = core\registry::singleton();
		$this->settings = $registry->getPlugin('users');
	}
	
	/*
	 * user login proccess
	 * @param array $e, form properties
	 * @return array, form properties
	 */
	public function login($e){
		if(trim($e['username']['VALUE']) == '' || trim($e['password']['VALUE'])==''){
			return browser\msg::modalNotComplete($e);
		}
		return $this->onclickLogin($e);
	}
	
	/*
	 * user logout proccess and refresh page
	 * @param array $e, form properties
	 * @return array, form properties
	 */
	public function logout($e){
		$validator = new network\validator;
		$validator->delete('USERS_LOGIN');
		$e['RV']['URL'] = 'R';
		return $e;
	}
	
	 /*
	  * check username is exists
	  * @param array $e, form properties
	  * @return array, form properties
	  */
	public function checkUsernameExists($e){
		$e['txtUsername']['MSG'] = '';
		$result = $this->checkUsername($e['txtUsername']['VALUE']);
		if($result == 1)
			$e['txtUsername']['MSG'] = _t('Username most be more than 3 character!');
		elseif($result == 2)
			$e['txtUsername']['MSG'] = _t('Only letters and digit allowed.');
		elseif($result == 3)
			$e['txtUsername']['MSG'] = _t('This username is taken by another.try again!');
		return $e;
	}
	 
	 /*
	  * check email is exists
	  * @param array $e, form properties
	  * @return array, form properties
	  */
	 public function checkEmailExists($e){
		 $e['txtEmail']['MSG'] = '';
		 $result = $this->checkEmail($e['txtEmail']['VALUE']);
		 if($result == 1)
			$e['txtEmail']['MSG'] = _t("format of entered email in incerrect.");
		 if($result == 2)
			$e['txtEmail']['MSG'] = _t("This email is taken by another.if that's you,use that to login to your account.");
		 return $e;
	 }
	 
	/*
	 * register user
	 * @param array $e, form properties
	 * @return array, form properties
	 */
	public function register($e){
		if($e['txtUsername']['VALUE'] == '' || $e['txtEmail']['VALUE'] == '')
			return browser\msg::modalNotComplete($e);
		//check username and email is valid
		elseif(!is_null($this->checkUsername($e['txtUsername']['VALUE'])) || !is_null($this->checkEmail($e['txtEmail']['VALUE']))){
			$e['RV']['MODAL'] = browser\page::showBlock(_t('Message'),_t('one or more of fileds invalid.please look at the messages of fileds.'),'MODAL','type-warning');
			return $e;
		}
		//check for that register is enable
		$registry = core\registry::singleton();
		$settings = $registry->getPlugin('users');
		if($settings->register == 0){
			$e['RV']['MODAL'] = browser\page::showBlock(_t('Message'),_t('Register new user on this site is disabled!'),'MODAL','type-danger');
			return $e;
		}
		//going to save user data
		$orm = db\orm::singleton();
		$validator = new network\validator;
		$user = $orm->dispense('users');
		$user->username = trim($e['txtUsername']['VALUE']);
		$user->email = trim($e['txtEmail']['VALUE']);
        $userPassword = core\general::randomString(10,'NC');
		$user->password = md5($userPassword);
		$user->permission = $this->settings->notActivePermission;
		$user->registerDate = time();
		
		//send email to user
		if($settings->active_from_email == 1){
            //ACTIVE WITH EMAIL
            $activeCode = $validator->set('USERS_ACTIVE',false,false);
            $user->permission = $this->settings->notActivePermission;
            $user->state = 'A:' . $activeCode;
            $header = _t('Active account');
            $body = '<strong>' . _t("your account created and you can active that by visit url that's comes below:") . '</strong></br>';
            $body .= sprintf('<a href="%s">%s</a>',core\general::createUrl(['users','activeAccount',$activeCode]),_t('For active your account click here!'));
            $e['RV']['MODAL'] = browser\page::showBlock(_t('Successfull'),_t('Your account was created and we send email for you. for active your account please check your inbox.'),'MODAL','type-success');
        }
        else{
            //ACTIVE AND SEND PASSWORD TO USER
            $user->permission = $this->settings->defaultPermission;
            $user->state = 'E';
            $header = _t('%s Registeration');
            $body = sprintf(_t('<strong>your account was created and your information is</strong></br>password:%s'),$userPassword);
            $e['RV']['MODAL'] = browser\page::showBlock(_t('Successfull'),_t('Your account was created and we send password to your email.please check your email.'),'MODAL','type-success');
        }
        $e['RV']['JUMP_AFTER_MODAL'] = DOMAIN_EXE;
        network\mail::simpleSend($user->username,$user->email,$header,$body);
        $orm->store($user);
		return $e;
		
	}
	
	/*
	 * active user account
	 * @param array $e, form properties
	 * @return array, form properties
	 */
	public function activeAcount($e){
		if($e['txtCode']['VALUE'] == '')
			return browser\msg::modalNotComplete($e);
		//going to active user account
		$orm = db\orm::singleton();
		$validator = new network\validator;
		if($validator->checkSid($e['txtCode']['VALUE']) && $orm->count('users','state=?',['A:'. $e['txtCode']['VALUE']]) != 0){
			$user = $orm->findOne('users','state=?',['A:'. $e['txtCode']['VALUE']]);
			$user->permission = $this->settings->defaultPermission;
			$user->state = '';
			$orm->store($user);
			$e['RV']['MODAL'] = browser\page::showBlock(_t('Successfull'),_t('Your account was activated.now you can login to your account.'),'MODAL','type-success');
			$e['RV']['JUMP_AFTER_MODAL'] = core\general::createUrl(['users','login']);
			//send active account message to user email
			
			return $e;
		}
		//show message
		$e['txtCode']['VALUE'] = '';
		$e['RV']['MODAL'] = browser\page::showBlock(_t('Fail!'),_t('Your entered key is invalid please try again!'),'MODAL','type-warning');
		return $e;
	}
	
	/*
	 * Delete ip from black list
	 * @param array $e,form properties
	 * @return array $e,form properties
	 */
	public function onclickBtnDeleteIp($e){
		if($this->hasAdminPanel()){
			$orm = db\orm::singleton();
			$orm->exec('DELETE FROM ipblock WHERE id=?',[$e['CLICK']['VALUE']],NON_SELECT);
			return browser\msg::modalsuccessfull($e,'R');
		}
		return browser\msg::modalNoPermission($e);
	}
	/*
	 * Delete ip from black list
	 * @param array $e,form properties
	 * @return array $e,form properties
	 */
	public function onclickBtnAddIp($e){
		if($this->hasAdminPanel()){
			if(data\type::isIp($e['txtIp']['VALUE']) == FALSE){
				$e['txtIp']['VALUE'] = '';
				return browser\msg::modal($e,_t('Error!'),_t('Entered ip is invalid please try another one.'),'warning');
			}
			$orm = db\orm::singleton();
			$ip = $orm->dispense('ipblock');
            $ip->ip = ip2long(trim($e['txtIp']['VALUE']));
            $orm->store($ip);
            return browser\msg::modalSuccessfull($e,['service','administrator','load','users','ipBlockList']);
		}
		return browser\msg::modalNoPermission($e);
	}
	
	
	/*
	 * change user password
	 * @param array $e,form properties
	 * @return array $e,form properties
	 */
	public function onclickBtnChangePassword($e){
		if($this->isLogedin()){
			if(strlen($e['txtPassword']['VALUE']) < 6 || strlen($e['txtRePassword']['VALUE']) < 6)
				return browser\msg::modal($e,('Warning') ,_t('Selected password is too short! password most be more than 6 characters.'),'warning');
			elseif($e['txtPassword']['VALUE'] != $e['txtRePassword']['VALUE'])
				return browser\msg::modal($e,('Warning'),_t('Entered passwords are not match'),'warning');
			$user = $this->getCurrentUserInfo();
			$orm = db\orm::singleton();
			$user->password = md5($e['txtPassword']['VALUE']);
			$orm->store($user);
            return browser\msg::modalSuccessfull($e,['users','profile']);
		}
		return browser\msg::modalNoPermission($e);
	}
	
	/*
	 * RESET USER PASSWORD
	 * @param array $e,form properties
	 * @return array $e,form properties
	 */
	public function btnOnclickResetPassword($e){
		if($e['txtEmail']['VALUE'] == '')
			return browser\msg::modalNotComplete($e);
		elseif(!data\type::isEmail($e['txtEmail']['VALUE']))
			return browser\msg::modal($e,('Warning'),_t('Format of entered email is incerrect!'),'warning');
		$orm = db\orm::singleton();
		if($orm->count('users','email=?',[$e['txtEmail']['VALUE']]) != 0){
			$validator = new network\validator;
			$localize = core\localize::singleton();
			$local = $localize->localize();
			$user = $orm->findOne('users','email=?',[$e['txtEmail']['VALUE']]);
			$user->forget = $validator->set('USERS_RESET',false,false);
			$orm->store($user);
			//send email to user
            $header = sprintf(_t('%s:Reset password'),$local->name);
            //set body of email
            $body = '<strong>' . $user->name . '</string></br>';
            $body .= sprintf(_t('We received a request to reset the password for your account at %s site.'),$local->name);
            $body .= _t("Here's a one-time login link for you to use to access your account and set a new password.");
            $body .= '<a href="' . core\general::createUrl(['users','resetPassword',$user->forget]) . '">' . _t('for login your account please click here!') . '</a></br>';
            $body .= _t("if you didn't request new login info, don't worry, this link will expire after a day and noting will happen if it's not used.");
            
            $e['RV']['MODAL'] = browser\page::showBlock(_t('Successfull'),_t('further instructions have been send to your email address.'),'MODAL','type-success');
			$e['RV']['JUMP_AFTER_MODAL'] = DOMAIN_EXE;
			network\mail::simpleSend($user->username,$user->email,$header,$body);
			return $e;
        }
		return browser\msg::modal($e,_t('Warning'), sprintf(_t('Sorry, %s is not recognized as a e-mail address.'),$e['txtEmail']['VALUE']),'warning');
		$e['txtEmail']['VALUE'] = '';
	}
	
	/*
	 * save user avatar
	 * @param array $e,form properties
	 * @return array $e,form properties
	 */
	public function btnOnclickSaveAvatar($e){
		if($this->isLogedin()){
			$registry = core\registry::singleton();
			$settings = $registry->getPlugin('users');
			if($settings->usersCanUploadAvatar == 1){
				if($e['userAvatar']['VALUE'] == '')
					return browser\msg::modal($e,_t('Error!'),_t('Please first upload your avatar.'),'warning');
				$user = $this->getCurrentUserInfo();
				$this->fileRemove($user->photo);
				$user->photo = $e['userAvatar']['VALUE'];
				$orm = db\orm::singleton();
				$orm->store($user);
				return browser\msg::modalSuccessfull($e,['users','profile']);
			}
		}
	}
	
	/*
	 * save user plugin settings
	 * @param array $e,form properties
	 * @return array $e,form properties
	 */
	public function btnOnclickRegisterSettings($e){
		if($this->hasAdminPanel()){
			//save register type
            $register_type = 0;
            if($e['rad_it_visitors']['CHECKED'] == '1')
                $register_type = 1;
            $registry = core\registry::singleton();
			$registry->set('users','register',$register_type);
			$registry->set('users','defaultPermission',$e['cobNewRoll']['SELECTED']);
			$registry->set('users','max_file_size',$e['txt_max_file_size']['VALUE']);
			//save email verification setting
            $user_can_upload = 0;
            if($e['ckb_user_pic']['CHECKED'] == '1')
                	$user_can_upload = 1;
            $registry->set('users','usersCanUploadAvatar',$user_can_upload);
			//save email verification setting
			$verification_type = 0;
			if($e['ckb_verification']['CHECKED'] == '1')
				$verification_type = 1;
			$registry->set('users','active_from_email',$verification_type);
			
			return browser\msg::modalSuccessfull($e);
		}
		return browser\msg::modalNoPermission($e);
	}
	
	/*
	 * save user plugin settings
	 * @param array $e,form properties
	 * @return array $e,form properties
	 */
	public function btnOnclickEditeUser($e){
		if($this->hasAdminPanel()){
			$orm = db\orm::singleton();
			if($orm->count('users','id=?',[$e['hidID']['VALUE']]) != 0){
				$orm->exec('UPDATE users SET permission=? WHERE id=?',[$e['cobUserRoll']['SELECTED'],$e['hidID']['VALUE']],NON_SELECT);
				return browser\msg::modalSuccessfull($e,['service','administrator','load','users','listPeople']);
			}
			return browser\msg::modalEventError($e);
		}
		return browser\msg::modalNoPermission($e);
	}
	
	/*
	 * insert new group
	 * @param array $e,form properties
	 * @return array $e,form properties
	 */
	public function btnOnclickNewGroup($e){
		if($this->hasAdminPanel()){
			if($e['txtName']['VALUE'] == '')
				return browser\msg::modalNotComplete($e);
			$orm = db\orm::singleton();
			if($orm->count('permissions','name=?',[$e['txtName']['VALUE']]) != 0){
				$e['txtName']['VALUE'] = '';
				return browser\msg::modal($e,_t('Group exists'),_t('Entered group is exists before.please try another name.'),'warning');
			}
			$permission = $orm->dispense('permissions');
			$permission->name = $e['txtName']['VALUE'];
			$permission->AdminPanel = 0;
			if($e['ckbAdminPanel']['CHECKED']) $permission->AdminPanel = 1;
			$permission->enable = 0;
			if($e['ckbActiveGroup']['CHECKED']) $permission->enable = 1;
			$orm->store($permission);
			return browser\msg::modalSuccessfull($e,['service','administrator','load','users','listGroups']);
		}
		return browser\msg::modalNoPermission($e);
	}

    /*
	 * edite exists group
	 * @param array $e,form properties
	 * @return array $e,form properties
	 */
    public function btnOnclickEditeGroup($e){
        if($this->hasAdminPanel()){
            if($e['txtName']['VALUE'] == '')
                return browser\msg::modalNotComplete($e);
            $orm = db\orm::singleton();
            if($orm->count('permissions','id=?',[$e['hidID']['VALUE']]) != 0){
                $permission = $orm->load('permissions',$e['hidID']['VALUE']);
                $permission->name = $e['txtName']['VALUE'];
                $permission->AdminPanel = 0;
                if($e['ckbAdminPanel']['CHECKED']) $permission->AdminPanel = 1;
                $permission->enable = 0;
                if($e['ckbActiveGroup']['CHECKED']) $permission->enable = 1;
                $orm->store($permission);
                return browser\msg::modalSuccessfull($e,['service','administrator','load','users','listGroups']);
            }
            return browser\msg::modalEventError($e);
        }
        return browser\msg::modalNoPermission($e);
    }

}
