<?php
namespace Mega\Apps\users;
use \Mega\Cls\Core as core;
use \Mega\Cls\Network as network;
use \Mega\Cls\Database as db;

trait addons {
	use \Mega\Apps\Administrator\addons;
	/*
	 * check for that user is logedin or not
	 * @return boolean (logedin:true, else:false)
	 */
	public function isLogedin(){
		$validator = new network\validator;
		if($validator->check('USERS_LOGIN')){
			$id = $validator->getID('USERS_LOGIN');
			$orm = db\orm::singleton();
			if($orm->count('users','login_key = ?',array($id)) != 0)
				return true;
		}
		return false;
	}
	
	/*
	 * get user id
	 * @param string $username, username
	 * @return integer user id,(not found:null)
	 */
	 public function getUserID($username){
		 $orm = db\orm::singleton();
		 if($orm->count('users','username=?',[$username]) != 0){
			 $user = $orm->findOne('users','username=?',[$username]);
			 return $user->id;
		 }
		 return null;
	 }
	 
	/*
	 * get user info
	 * @param string $username
	 * @return object of user info (null = guest)
	 */
	public function getInfo($username = null){
		$orm = db\orm::singleton();
		if($orm->count('users','username=?',[$username]) != 0)
			return $orm->findOne('users','username=?',[$username]);
		return null;
	}
	
	/*
	 * get cerrent user info
	 * @return object of user info (null = guest)
	 */
	public function getCurrentUserInfo(){
		$validator = new network\validator;
		if($validator->check('USERS_LOGIN')){
			$id = $validator->getID('USERS_LOGIN');
			$orm = db\orm::singleton();
			if($orm->count('users','login_key=?',[$id]) != 0)
				return $orm->findOne('users','login_key=?',[$id]);
		}
		return null;
	}
	
	/*
	 * check for username is exists
	 * @param string $username, username
	 * @return boolean(true:exists, false:not exists)
	 */
	public function existsUsername($username){
		$orm = db\orm::singleton();
		if($orm->count('users','username=?',[$username]) != 0)
			return true;
		return false;
	}
	
	/*
	 * check for email is exists
	 * @param string $email, email
	 * @return boolean(true:exists, false:not exists)
	 */
	public function existsEmail($email){
		$orm = db\orm::singleton();
		if($orm->count('users','email=?',[$email]) != 0)
			return true;
		return false;
	}
	
	/*
	 * check Username for be valid
	 * @param string $username, username
	 * @return integer(null: valid username, 1:less than 4 character, 2:invalid format, 3:is exist before)
	 */
	public function checkUsername($username){
		if(strlen($username) < 4)
			return 1;
		elseif(!preg_match("/^[a-zA-Z0-9]*$/",$username))
			return 2;
		elseif($this->existsUsername($username))
			return 3;
		return null;
	}
	
	/*
	 * check Email for be valid
	 * @param string $email, email
	 * @return integer(null: valid email, 1:invalid format, 2:is exist before)
	 */
	public function checkEmail($email){
		if(!filter_var($email,FILTER_VALIDATE_EMAIL))
			return 1;
		if($this->existsEmail($email))
			return 2;
		return null;
	}
	
	/*
	 * use for login user to system by username
	 * @param string $username, username
	 * @return boolean
	 */
	public function loginWithUsername($username){
		$orm = db\orm::singleton();
		$count = $orm->count('users',"username = ?", [$username]);
		if($count != 0){
			//login data is cerrect
			$validator = new network\validator;
			$validID = $validator->set('USERS_LOGIN',true);
			//INSERT VALID ID IN USER ROW
			$user = $orm->load('users',$this->getUserID($username));
			$user->login_key = $validID;
			$user->last_login = time();
			$orm->store($user);
			return true;
		}
		return false;
		
	}
	
	/*
	 * check for that user has permission to do task
	 * @param string $permission, permission name
	 * @param string $username ,(null: current user)
	 * @return boolean
	 */
	public function hasPermission($permission,$username=null){
		$orm = db\orm::singleton();
		if($username == ''){
				//get cerrent user info
				$user = $this->getCurrentUserInfo();
				if($user == null){
					//get guest info
					$registry = core\registry::singleton();
					$id = $registry->get('users','guestPermission');
				}
				else
					$id = $user->permission;
				$per = $orm->findOne('permissions',"id = ?", array($id));
				if($per->$permission == '1'){return true;}
				return false;
			}
			else{
				//get permission with username
				//check for that user exists
				if($orm->count('users',"username = ?",array($username)) != 0){
					//going to find permission
					$res = $orm->getRow('SELECT * FROM users s INNER JOIN permissions p ON s.permission=p.id where s.username=?',array($username));
					//checking for that permission is exist
					if(array_key_exists($permission,$res))
						if($res->$permission == '1')
							return true;
				}
			}
	}
	
	/*
	 * function for add new permission
	 * @param string $name, permission name
	 * @param boolean $default, default permission
	 * @return boolean
	 */
	public function newPermission($name,$default){
		$name = htmlspecialchars($name);
		$defaultString = 0;
		if($default) $defaultString = 1;
		$orm = db\orm::singleton();
        if(!$orm->columnExists('permissions',$name)){
            $orm->exec('ALTER TABLE permissions ADD ' . $name . " VARCHAR(1) DEFAULT '" . $defaultString . "';",[],NON_SELECT);
            $orm->exec("UPDATE permissions SET " . $name . "='1' WHERE name='Administrators';",[],NON_SELECT);
        }
	}

    /*
     * get all user count
     * @return integer
     */
    public function usersCount(){
        $orm = db\orm::singleton();
        return $orm->count('users');
    }

    /*
     * return last registered member
     * @return string last username
     */
    public function lastRegisteredUser(){
        $orm = db\orm::singleton();
        $user = $orm->exec('SELECT * FROM users ORDER BY id DESC;',[],SELECT_ONE_ROW);
        return $user->username;
    }
}
