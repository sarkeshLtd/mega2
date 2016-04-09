<?php
namespace Mega\Apps\users;
use \Mega\control as control;
use \Mega\cls\core as core;
use \Mega\Cls\Database as db;
use \Mega\cls\calendar as calendar;

trait view {
	/*
	 * show login form
	 * @return string, html content
	 */
	protected function frmLogin($position = 'block'){
		$form = new control\form('frmUsersLogin');
		if(defined('PLUGIN_OPTIONS')){
			$jump = new control\hidden('hidJump');
			$jump->value = PLUGIN_OPTIONS;
			$form->add($jump);
		}
		
		$username = new control\textbox('username');
		$username->INLINE = TRUE;
		$username->PLACE_HOLDER = _('Username or e-mail address');
		
		$password = new control\textbox('password');
		$password->INLINE = TRUE;
		$password->label = _('Password:');
		$password->PLACE_HOLDER = _('Password');
		$password->PASSWORD = true;
		
		$remember = new control\checkbox('ckbRemember');
		$remember->LABEL = _('Remember me!');
        $remember->size=12;
		
		$login = new control\button('btnLogin');
		$login->LABEL = _('Sign in');
		$login->P_ONCLICK_PLUGIN = 'users';
		$login->P_ONCLICK_FUNCTION = 'login';
		$login->TYPE = 'primary';
		
		$forget = new control\button('btnResetPassword');
		$forget->LABEL = _('Reset Password');
		$forget->HREF = core\general::createUrl(['users','resetPassword']);
		$forget->TYPE = 'link';
		$forget->size=12;

		$r = new control\row;
		$r->add($login,2);
		$r->add($forget,10);
		
		$form = new control\form;
		$form->NAME = 'usersLoginFrm';
		if($position == 'block') $form->NAME = 'usersLoginWidget';
		$form->addArray([$username,$password,$remember,$r]);
		//users can register?
		$registry = core\registry::singleton();
		
		if($registry->get('users','register') == '1'){
			$form->add_spc();
			$lbl = new control\label(_("Don't have account?"));
			$register = new control\button;
			$register->configure('NAME','btn_register');
			$register->configure('LABEL', _('Sign up'));
			$register->configure('HREF',core\general::createUrl(['users','register']));
			$register->configure('TYPE','success');
			$r1 = new control\row;
			$r1->add($lbl,7);
			$r1->add($register,5);
			$form->add($r1);
		}
		
		return [_('Sign in'),$form->draw()];		
	}
	
	/*
	 * show minimal profile in widget mode
	 * @param object $user, user info
	 * @param boolean $adminAccess, can user access to administrator area
	 * @return string, html content
	 */
	protected function viewWidgetProfile($user,$adminAccess){
		 $form = new control\form('usersProfileWidget');
		 //add profile
		 if($user->photo != ''){
			 if($this->fileExists($user->photo)){
				 $imgAvatar = new control\image('imgAvatar');
				 $imgAvatar->src = $this->getFileAddress($user->photo);
                 $imgAvatar->style = "width:150px;";
				 $form->add($imgAvatar);
			 }	 
		 }
		 $label = new control\label(sprintf(_('Hello %s !'),$user->username));
		 $form->add($label);
		 $row = new control\row;
		 $btn_logout = new control\button;
		 $btn_logout->configure('NAME','btn_logout');
		 $btn_logout->configure('LABEL',_('Sign Out!'));
		 $btn_logout->configure('TYPE','info');
		 $btn_logout->configure('P_ONCLICK_PLUGIN','users');
		 $btn_logout->configure('P_ONCLICK_FUNCTION','logout');
		 $row->add($btn_logout,4);
		 
		 $btnProfile = new control\button;
		 $btnProfile->configure('NAME','btn_logout');
		 $btnProfile->configure('LABEL',_('Your profile'));
		 $btnProfile->configure('TYPE','default');
		 $btnProfile->configure('HREF',core\general::createUrl(['users','profile']));
		 $row->add($btnProfile,4);
		 
		 if($adminAccess){
			$btn_admin = new control\button;
			$btn_admin->configure('NAME','JUMP_ADMIN');
			$btn_admin->configure('LABEL',_('Admin panel'));
			$btn_admin->configure('HREF',core\general::createUrl(['service','administrator','load','administrator','dashboard']));
			$row->add($btn_admin,4);
		 }
		 
		 $form->add($row);
		 return array(_('User Profile'),$form->draw());
	}
	
	/*
	 * show register form
	 * @return string, html content
	 */
	protected function viewFrmRegister(){
		$form = new control\form('frmUsersRegister');
		
		$txtUsername = new control\textbox('txtUsername');
		$txtUsername->label = _('Username:');
		$txtUsername->size = 5;
		$txtUsername->place_Holder = _('Username');
		$txtUsername->help = _('Only letters and digits allowed. special characters and space not allowed.');
		$txtUsername->P_ONBLUR_PLUGIN = 'users';
		$txtUsername->P_ONBLUR_FUNCTION = 'checkUsernameExists';
		$form->add($txtUsername);
		
		$txtEmail = new control\textbox('txtEmail');
		$txtEmail->label = _('Email:');
		$txtEmail->help = _('A valid e-mail address. All e-mails from the system will be sent to this address. The e-mail address is not made public and will only be used if you wish to receive a new password or wish to receive certain news or notifications by e-mail.');
		$txtEmail->size = 6;
		$txtEmail->P_ONBLUR_PLUGIN = 'users';
		$txtEmail->P_ONBLUR_FUNCTION = 'checkEmailExists';
		$txtEmail->place_Holder = _('Email');
		$form->add($txtEmail);
		
		
		$btnSignup = new control\button('btn_signup');
		$btnSignup->type = 'primary';
		$btnSignup->label = _('Create new account');
		$btnSignup->P_ONCLICK_PLUGIN = 'users';
		$btnSignup->P_ONCLICK_FUNCTION = 'register';
		 
		$btnCancel = new control\button;
		$btnCancel->configure('NAME','btn_cancel');
		$btnCancel->configure('TYPE','warning');
		$btnCancel->configure('LABEL',_('Cancel'));
		$btnCancel->configure('HREF',DOMAIN_EXE);
		 
		$row = new control\row();
		$row->add($btnSignup,3);
		$row->add($btnCancel,2);
		$form->add($row);
		
		return [_('Register'),$form->draw()];
	}
	
	/*
	 * show active form
	 * @param string $activator,activator code
	 * @return string, html content
	 */
	protected function viewActiveAccount($msg = ''){
		$form = new control\form('usersActiveAcount');
		
		$txtCode = new control\textbox('txtCode');
		$txtCode->size = 4;
		$txtCode->label = _('Activator code:');
		$txtCode->help = _('Enter activator code that you get from your email');
		$form->add($txtCode);
		
		$btnSubmit = new control\button('btnSubmit');
		$btnSubmit->label = _('Active account');
		$btnSubmit->type = 'primary';
		$btnSubmit->P_ONCLICK_PLUGIN = 'users';
		$btnSubmit->P_ONCLICK_FUNCTION = 'activeAcount';
		
		$form->add($btnSubmit);
		
		return [_('Active account'),$form->draw()];
	}
	
	/*
	 * show active form
	 * @param string $activator,activator code
	 * @return string, html content
	 */
	protected function viewResetPassword(){
		$form = new control\form('UsersResetPassword');
		
		$txtCode = new control\textbox('txtEmail');
		$txtCode->size = 5;
		$txtCode->label = _('Email:');
		$txtCode->help = _('Enter your email address that you register with that.');
		$form->add($txtCode);
		
		$btnSubmit = new control\button('btnSubmit');
		$btnSubmit->label = _('Send request');
		$btnSubmit->type = 'primary';
		$btnSubmit->P_ONCLICK_PLUGIN = 'users';
		$btnSubmit->P_ONCLICK_FUNCTION = 'btnOnclickResetPassword';
		
		$btn_cancel = new control\button('btn_cancel');
		$btn_cancel->configure('LABEL',_('Cancel'));
		$btn_cancel->configure('HREF',DOMAIN_EXE);
				
		$row = new control\row;
		$row->configure('IN_TABLE',false);
		$row->add($btnSubmit,2);
		$row->add($btn_cancel,10);
		$form->add($row);
		
		return [_('Reset password'),$form->draw()];
	}
	
	/*
	 * show list of blocked ips
	 * @param array $ips, list of all blocked ips that fetch from database
	 * @return string, html content
	 */
	protected function viewIpBlockList($ips){
		$form = new control\form("users_list_ip_blocked");
		$form->configure('LABEL',_('Blocked IPs'));
				
		$table = new control\table;
		$key=1;
		foreach($ips as $key=>$ip){
			$row = new control\row;
					
			//add id to table for count rows
			$lbl_id = new control\label($key);
			$key++;
			$row->add($lbl_id,1);
					
			//add ip
			$lbl_ip = new control\label(long2ip($ip->ip));
			$row->add($lbl_ip,2);
		            

			//add edite button
			$btn_remove = new control\button;
			$btn_remove->configure('LABEL',_('Delete'));
			$btn_remove->configure('TYPE','info');
			$btn_remove->configure('VALUE',$ip->id);
			$btn_remove->configure('P_ONCLICK_PLUGIN','users');
			$btn_remove->configure('P_ONCLICK_FUNCTION','onclickBtnDeleteIp');
			$row->add($btn_remove,1);
			$table->add_row($row);
					
		}
				
		//add headers to table
		$table->configure('HEADERS',array(_('ID'),_('IP number'),_('Options')));
		$table->configure('HEADERS_WIDTH',[1,5,2]);
		$table->configure('ALIGN_CENTER',[TRUE,FALSE,TRUE]);
		$table->configure('BORDER',true);
		$form->add($table);

		//update and cancel buttons
		$btn_update = new control\button('btn_add_new');
		$btn_update->configure('LABEL',_('Add IP'));
		$btn_update->configure('HREF',core\general::createUrl(['service','administrator','load','users','newIpBlock']));
		$btn_update->configure('TYPE','primary');
				
		$btn_cancel = new control\button('btn_cancel');
		$btn_cancel->configure('LABEL',_('Cancel'));
		$btn_cancel->configure('HREF',core\general::createUrl(['service','administrator','load','administrator','dashboard']));
				
		$row = new control\row;
		$row->configure('IN_TABLE',false);
		$row->add($btn_update,1);
		$row->add($btn_cancel,11);
		$form->add($row);
		return [_('Blocked IPs'),$form->draw()];
	}
	
	/*
	 * add new ip to block list
	 * @return string, html content
	 */
	protected function viewNewIpBlock(){
		$form = new control\form('frm_new_ip_block');
	  	$txtIp = new control\textbox('txtIp');
	  	$txtIp->configure('LABEL',_('IP Address'));
	  	$txtIp->configure('ADDON',_('*'));
	  	$txtIp->configure('SIZE',4);
		$txtIp->configure('HELP',_('Enter ip that you want will be blocked by system. '));
		$form->add($txtIp);
		//update and cancel buttons
		$btnUpdate = new control\button('btnUpdate');
		$btnUpdate->configure('LABEL',_('Add'));
		$btnUpdate->configure('P_ONCLICK_PLUGIN','users');
		$btnUpdate->configure('P_ONCLICK_FUNCTION','onclickBtnAddIp');
		$btnUpdate->configure('TYPE','primary');
		
		$btnCancel = new control\button('btnCancel');
		$btnCancel->configure('LABEL',_('Cancel'));
		$btnCancel->configure('HREF',core\general::createUrl(['service','administrator','load','users','ipBlockList']));
		
		$row = new control\row;
		$row->configure('IN_TABLE',false);
		$row->add($btnUpdate,1);
		$row->add($btnCancel,11);
		$form->add($row);
	  	return [_('Block new ip'),$form->draw()];
	}
	
	protected function viewFailActiveAccount(){
		$form = new control\form('frmFailActive');
		$lblMsg = new control\label('<strong>' . _('Your enterd activator code is invalid or be expaird.') . '</strong>');
		
		$row = new control\row;
		$btnResend = new control\button('btnResendActivator');
		$btnResend->label = _('Request new activator code');
		$btnResend->href = core\general::createUrl(['users','requestActivator']);
		$btnResend->type = 'success';
		$row->add($btnResend,2);
		
		$btnHome = new control\button('btnHome');
		$btnHome->href = DOMAIN_EXE;
		$btnHome->label = _('Home');
		$row->add($btnHome,10);
		
		$form->addArray([$lblMsg,$row]);
		return [_('Error!'),$form->draw()];
	}
	
	/*
	 * change user password
	 * @return string, html content
	 */
	protected function viewChangePassword(){
		$form = new control\form('frmChangePassword');
		if(defined('PLUGIN_OPTIONS')){
			$lblNewUser = new control\label('lblNewUser');
			$lblNewUser->label = _('Your account was created and you can set your password.');
			$form->add($lblNewUser);
		}
		$txtPassword = new control\textbox('txtPassword');
		$txtPassword->place_holder = _('New password');
		$txtPassword->label = _('New password');
		$txtPassword->password = true;
		$txtPassword->size = 4;
		$form->add($txtPassword);
		
		$txtRePassword = new control\textbox('txtRePassword');
		$txtRePassword->label = _('Confrim new password');
		$txtRePassword->place_holder = _('Confrim new password');
		$txtRePassword->password = true;
		$txtRePassword->size = 4;
		$form->add($txtRePassword);
		
		//update and cancel buttons
		$btnUpdate = new control\button('btnUpdate');
		$btnUpdate->configure('LABEL',_('Set new password'));
		$btnUpdate->configure('P_ONCLICK_PLUGIN','users');
		$btnUpdate->configure('P_ONCLICK_FUNCTION','onclickBtnChangePassword');
		$btnUpdate->configure('TYPE','primary');
		
		$btnCancel = new control\button('btnCancel');
		$btnCancel->configure('LABEL',_('Cancel'));
		$btnCancel->configure('HREF',core\general::createUrl(['users','profile']));
		
		$row = new control\row;
		$row->configure('IN_TABLE',false);
		$row->add($btnUpdate,2);
		$row->add($btnCancel,10);
		$form->add($row);
		
		return [ _('Set password'),$form->draw()];
	}
	
	/*
	 * show expire validator for reset password
	 * @return string, html content
	 */
	protected function viewResetPasswordExpire(){
		$form = new control\form('frmUsersExpireResetPassword');
		$msg = new control\wall('wallExpire');
		$msg->value = _('your code expired or not valid.please try again for send new request.');
		$msg->type = 'warning';
		$form->add($msg);
		
		//update and cancel buttons
		$btnNewCode = new control\button('btnNewCode');
		$btnNewCode->configure('LABEL',_('Request again'));
		$btnNewCode->type = 'primary';
		$btnNewCode->configure('HREF',core\general::createUrl(['users','resetPassword']));
		
		$btnCancel = new control\button('btnCancel');
		$btnCancel->configure('LABEL',_('Cancel'));
		$btnCancel->configure('HREF',DOMAIN_EXE);
		
		$row = new control\row;
		$row->configure('IN_TABLE',false);
		$row->add($btnNewCode,2);
		$row->add($btnCancel,10);
		$form->add($row);
		
		return [_('fail reset password'),$form->draw()];
	}
	
	/*
	 * show user own profile
	 * @param object $user, user information
	 * @return array
	 */
	protected function viewOwnProfile($user){
		$form = new control\form('frmUserOwnProfile');
		$tile = new control\tile('usersProfile');
		
		$avatar = new control\image('imgAvatar');
		$avatar->src = $user->photo;
		$row = new control\row('rowProfile');
		$row->in_table = false;
		$row->add($avatar,2);
		
		$lblUsername = new control\label(_('Username:') . $user->username);
		$form->add($lblUsername);
		
		//show register date
		$calendar = calendar\calendar::singleton();
		$registry = core\registry::singleton();
		$settings = $registry->getPlugin('users');
		$lblRegisterDate = new control\label(_('Register Date:') . $calendar->cdate($settings->registerDateFormat,$user->registerDate));
		$form->add($lblRegisterDate);
		
		$rowButtons = new control\row;
		$btnChangePassword = new control\button('btnChangePassword');
		$btnChangePassword->type = 'default';
		$btnChangePassword->label = _('Change password');
		$btnChangePassword->href = core\general::createUrl(['users','changePassword']);
		$rowButtons->add($btnChangePassword);
		
		if($settings->usersCanUploadAvatar == 1){
			$btnChangeAvatar = new control\button('btnChangeAvatar');
			$btnChangeAvatar->type = 'primary';
			$btnChangeAvatar->label = _('Change your avatar');
			$btnChangeAvatar->href = core\general::createUrl(['users','changeAvatar']);
			$rowButtons->add($btnChangeAvatar);
		}
		$form->add($rowButtons);
		$row->add($form,10);
		$tile->add($row);	
		return [sprintf(_("%s's profile"),$user->username),$tile->draw()];
	}
	
	/*
	 * show page to user for select new or change avatar
	 * @param array $settings , plugin settings
	 * @return array, [title,body]
	 */
	protected function viewChangeAvatar($settings){
		$form = new control\form('usersChangeAvatar');
		$uploader = new control\uploader('userAvatar');
		$uploader->MAX_FILE_SIZE = $settings->max_file_size * 8;
		$uploader->label = _('Upload avatar');
		$uploader->help = _('first upload your avatar and click on save button');
		$form->add($uploader);
		
		//update and cancel buttons
		$btnUpdateAvatar = new control\button('btnNewCode');
		$btnUpdateAvatar->configure('LABEL',_('Save avatar'));
		$btnUpdateAvatar->type = 'primary';
		$btnUpdateAvatar->p_onclick_plugin = 'users';
		$btnUpdateAvatar->p_onclick_function = 'btnOnclickSaveAvatar';
		
		$btnCancel = new control\button('btnCancel');
		$btnCancel->configure('LABEL',_('Cancel'));
		$btnCancel->configure('HREF',core\general::createUrl(['users','profile']));
		
		$row = new control\row;
		$row->configure('IN_TABLE',false);
		$row->add($btnUpdateAvatar,2);
		$row->add($btnCancel,10);
		
		$form->add($row);
		return [_('Change avatar'),$form->draw()];
	}
	
	/*
	 * show list of people in administrator area
	 * @param array $users, users informations
	 * @param array $froups, groups informations
	 * @return array, [title,body]
	 */
	protected function viewListPeople($users,$groups){
		$form = new control\form("users_list_people");
		$form->configure('LABEL',_('People'));
		
		$table = new control\table;
		
		foreach($users as $key=>$user){
			$row = new control\row;
			
			//add id to table for count rows
			$lbl_id = new control\label($key+1);
			$row->add($lbl_id,1);
			
			//add user name
			$lbl_user_name = new control\label($user->username);
			$row->add($lbl_user_name,2);
            
            //add user group
            foreach($groups as $group){
                if($group->id == $user->permission){
                    $lbl_user_group = new control\label($group->name);
                }
            }
			
			$row->add($lbl_user_group,2);
			
			//add register date
            //show last login date
            $calendar = new calendar\calendar;
            $registry = core\registry::singleton();
            $settings = $registry->getPlugin('users');
		   	$lbl_register = new control\label($calendar->cdate($settings->registerDateFormat,$user->registerDate ));
			$row->add($lbl_register,2);
			
			//add edite button
            $btn_active = new control\button;
            $btn_active->configure('LABEL',_('Edite'));
            $btn_active->configure('TYPE','success');
            $btn_active->configure('VALUE',$user->id);
			$btn_active->configure('HREF',core\general::createUrl(['service','administrator','load','users','editeUser',$user->id]));
			$row->add($btn_active,1);
			
			$table->add_row($row);
		}
		
		//add headers to table
		$table->configure('HEADERS',array(_('ID'),_('Username'),_('Group'),_('Register Date'),_('Options')));
		$table->configure('HEADERS_WIDTH',[1,5,2,2,2]);
		$table->configure('ALIGN_CENTER',[TRUE,FALSE,TRUE,TRUE,TRUE]);
		$table->configure('BORDER',true);
		$form->add($table);
		
		return array(_('People'),$form->draw());
	}
	
	/*
	 * SHOW USER PLUGIN SETTINGS
	 * @param array $settings, users plugin settings
	 * @param array $permissions, all permissions
	 * @return array, [title,body]
	 */
	protected function viewAccountSettings($settings,$rolls){

	  	$form = new control\form('form');
		$form->configure('LABEL',_('Registration settings'));

		//add default roll for new users
		$cobNewRoll = new control\combobox('cobNewRoll');
	    $cobNewRoll->configure('LABEL',_('Default users roll'));
	    $cobNewRoll->configure('HELP',_('New users get roll that you select in above.'));
	    $cobNewRoll->configure('TABLE',$rolls);
	    $cobNewRoll->configure('COLUMN_VALUES','id');
	    $cobNewRoll->configure('COLUMN_LABELS','name');
	    $cobNewRoll->configure('SELECTED_INDEX',$settings->defaultPermission);
	    $cobNewRoll->configure('SIZE',3);
	    $form->add($cobNewRoll);

		$rad_bot = new control\radiobuttons('rad_show_option');
		$rad_bot->configure('LABEL',_('Who can register accounts? '));
		$radit_admin_only = new control\radioitem('rad_it_adminonly');
		$radit_admin_only->configure('LABEL',_('Administrators only'));
		if($settings->register == 0)
			$radit_admin_only->configure('CHECKED',TRUE);
		$rad_bot->add($radit_admin_only);
			
		$radit_visitors  = new control\radioitem('rad_it_visitors');
		$radit_visitors->configure('LABEL',_('Visitors'));
		if($settings->register == 1)
			$radit_visitors->configure('CHECKED',TRUE);
		$rad_bot->add($radit_visitors);
		$form->add($rad_bot);

		//veriflication settings
		$ckb_verification = new control\checkbox('ckb_verification');
		$ckb_verification->configure('LABEL',_('Require e-mail verification when a visitor creates an account.') );
		$ckb_verification->configure('HELP',_('New users will be required to validate their e-mail address prior to logging into the site, and will be assigned a system-generated password. With this setting disabled, users will be logged in immediately upon registering, and may select their own passwords during registration.'));
		if($settings->active_from_email == 1)
			$ckb_verification->configure('CHECKED',TRUE);
		$form->add($ckb_verification);

		//enable user picture
		$ckb_user_pic = new control\checkbox('ckb_user_pic');
		$ckb_user_pic->configure('LABEL',_('Enable user pictures. ') );
		$ckb_user_pic->configure('HELP',_('With this option,site users can upload personal avatars.'));
		if($settings->usersCanUploadAvatar == 1)
			$ckb_user_pic->configure('CHECKED',TRUE);	
		$form->add($ckb_user_pic);
		  	
		//max_file_size
		$txt_max_file_size = new control\textbox('txt_max_file_size');
		$txt_max_file_size->configure('LABEL',_('Picture upload max file size'));
		$txt_max_file_size->configure('ADDON',_('KiloByte'));
		$txt_max_file_size->configure('VALUE',$settings->max_file_size);
		$txt_max_file_size->configure('SIZE',3);
		$txt_max_file_size->configure('HELP',_('Maximum allowed file size for uploaded pictures. Upload size is normally limited only by the PHP maximum post and file upload settings, and images are automatically scaled down to the dimensions specified above.'));
		$form->add($txt_max_file_size);

		//update and cancel buttons
		$btn_update = new control\button('btn_update');
		$btn_update->configure('LABEL',_('Update'));
		$btn_update->configure('P_ONCLICK_PLUGIN','users');
		$btn_update->configure('P_ONCLICK_FUNCTION','btnOnclickRegisterSettings');
		$btn_update->configure('TYPE','primary');
			
		$btn_cancel = new control\button('btn_cancel');
		$btn_cancel->configure('LABEL',_('Cancel'));
		$btn_cancel->configure('HREF',core\general::createUrl(['service','administrator','load','administrator','dashboard']));
			
		$row = new control\row;
		$row->configure('IN_TABLE',false);
		$row->add($btn_update,1);
		$row->add($btn_cancel,11);
		$form->add($row);
  	
		return [_('Account settings'),$form->draw()];
	}
	
	/*
	 * edite user information
	 * @param object $user, user information
	 * @param array $permissions, all permissions data
	 * @param array $settings, plugin settings
	 * @return array, [title,body]
	 */
	protected function viewEditeUser($user, $permissions,$settings){
		$form = new control\form('frmUsersEditeUser');
		
		//set user id
		$hidID = new control\hidden('hidID');
		$hidID->value = $user->id;
		$form->add($hidID);
		
		//change user roll
		$cobNewRoll = new control\combobox('cobUserRoll');
	    $cobNewRoll->configure('LABEL',_('Users roll'));
	    $cobNewRoll->configure('HELP',_('Be carefull about change user roll.'));
	    $cobNewRoll->configure('TABLE',$permissions);
	    $cobNewRoll->configure('COLUMN_VALUES','id');
	    $cobNewRoll->configure('COLUMN_LABELS','name');
	    $cobNewRoll->configure('SELECTED_INDEX',$settings->defaultPermission);
	    $cobNewRoll->configure('SIZE',3);
	    $form->add($cobNewRoll);
	    
	    //update and cancel buttons
		$btn_update = new control\button('btn_update');
		$btn_update->configure('LABEL',_('Update'));
		$btn_update->configure('P_ONCLICK_PLUGIN','users');
		$btn_update->configure('P_ONCLICK_FUNCTION','btnOnclickEditeUser');
		$btn_update->configure('TYPE','primary');
			
		$btn_cancel = new control\button('btn_cancel');
		$btn_cancel->configure('LABEL',_('Cancel'));
		$btn_cancel->configure('HREF',core\general::createUrl(['service','administrator','load','users','listPeople']));
			
		$row = new control\row;
		$row->configure('IN_TABLE',false);
		$row->add($btn_update,1);
		$row->add($btn_cancel,11);
		$form->add($row);
	    return [sprintf(_('Edite %s information and rolls'),$user->username),$form->draw()];
	}
	
	/*
	 * show list of groups
	 * @param array $permissions, all exists permissions
	 * @return array, [title,body]
	 */
	protected function viewListGroups($permissions){
        $form = new control\form("users_list_people");
		$form->configure('LABEL',_('User Groups'));
		
		$table = new control\table;
		$orm = db\orm::singleton();
		foreach($permissions as $key=>$group){
			$row = new control\row;
			
			//add id to table for count rows
			$lbl_id = new control\label($key+1);
			$row->add($lbl_id,1);
			
			//add group name
			$lbl_group_name = new control\label($group->name);
			$row->add($lbl_group_name,2);
            
            $user_number = new control\label($orm->count('users','permission=?',[$group->id]));	
			$row->add($user_number,2);

			//add edite button
            $btn_active = new control\button;
            $btn_active->configure('LABEL',_('Edite'));
            $btn_active->configure('TYPE','success');
			$btn_active->configure('HREF',core\general::createUrl(['service','administrator','load','users','editeGroup',$group->id]));
			$row->add($btn_active,1);
			$table->add_row($row);
		}
		//add headers to table
		$table->configure('HEADERS',array(_('ID'),_('Name'),_('Count'),_('Options')));
		$table->configure('HEADERS_WIDTH',[1,7,2,2]);
		$table->configure('ALIGN_CENTER',[TRUE,FALSE,TRUE,TRUE]);
		$table->configure('BORDER',true);
		$form->add($table);
		
		$btn_insert_group = new control\button('btn_insert_group');
		$btn_insert_group->configure('LABEL',_('New Group'));
		$btn_insert_group->configure('TYPE','primary');
		$btn_insert_group->configure('HREF',core\general::createUrl(['service','administrator','load','users','newGroup']));
		
		$btn_cancel = new control\button('btn_cancel');
		$btn_cancel->configure('LABEL',_('Cancel'));
		$btn_cancel->configure('HREF',core\general::createUrl(['service','administrator','load','administrator','dashboard']));
		
		$row = new control\row;
		$row->configure('IN_TABLE',false);
		
		$row->add($btn_insert_group,1);
		$row->add($btn_cancel,11);
		$form->add($row);
		return array(_('Groups'),$form->draw());
	}
	
	/*
	 * show form for add new group
	 * @return array, [title,body]
	 */
	protected function viewNewGroup(){
		$form = new control\form('frmUsersNewGroup');
		
		$txtName = new control\textbox('txtName');
		$txtName->label = _('Group name');
		$txtName->place_holder = _('Group name');
		$txtName->help = _('Group name show in users profile and you can control user permissions of this group with this.');
		$txtName->size = 4;
		$form->add($txtName);
		
		$ckbActiveGroup = new control\checkbox('ckbActiveGroup');
		$ckbActiveGroup->configure('LABEL',_('active group') );
		$ckbActiveGroup->configure('HELP',_('with this option you can select access to site for users of this group.'));
		$ckbActiveGroup->configure('CHECKED',TRUE);
		$form->add($ckbActiveGroup);
		
		$ckbAdminPanel = new control\checkbox('ckbAdminPanel');
		$ckbAdminPanel->configure('LABEL',_('Admin area?') );
		$ckbAdminPanel->configure('HELP',_('If you check this option,users of this group can access to administrator area.'));
		$ckbAdminPanel->configure('CHECKED',FALSE);
		$form->add($ckbAdminPanel);
		
		$btn_insert_group = new control\button('btn_insert_group');
		$btn_insert_group->configure('LABEL',_('New Group'));
		$btn_insert_group->configure('TYPE','primary');
		$btn_insert_group->p_onclick_plugin = 'users';
		$btn_insert_group->p_onclick_function = 'btnOnclickNewGroup';
		
		$btn_cancel = new control\button('btn_cancel');
		$btn_cancel->configure('LABEL',_('Cancel'));
		$btn_cancel->configure('HREF',core\general::createUrl(['service','administrator','load','users','listGroups']));
		
		$row = new control\row;
		$row->configure('IN_TABLE',false);
		
		$row->add($btn_insert_group,1);
		$row->add($btn_cancel,11);
		$form->add($row);
		return [_('New group'),$form->draw()];
	}

    /*
	 * show form edite group
     * @param object $group, group information
	 * @return array, [title,body]
	 */
    protected function viewEditeGroup($group){
        $form = new control\form('frmUsersNewGroup');

        $hidID = new control\hidden('hidID');
        $hidID->value = $group->id;
        $form->add($hidID);

        $txtName = new control\textbox('txtName');
        $txtName->label = _('Group name');
        $txtName->value = $group->name;
        $txtName->place_holder = _('Group name');
        $txtName->help = _('Group name show in users profile and you can control user permissions of this group with this.');
        $txtName->size = 4;
        $form->add($txtName);

        $ckbActiveGroup = new control\checkbox('ckbActiveGroup');
        $ckbActiveGroup->configure('LABEL',_('active group') );
        $ckbActiveGroup->configure('HELP',_('with this option you can select access to site for users of this group.'));
        $ckbActiveGroup->configure('CHECKED',TRUE);
        if($group->enable == 0)
            $ckbActiveGroup->configure('CHECKED',FALSE);
        $form->add($ckbActiveGroup);

        $ckbAdminPanel = new control\checkbox('ckbAdminPanel');
        $ckbAdminPanel->configure('LABEL',_('Admin area?') );
        $ckbAdminPanel->configure('HELP',_('If you check this option,users of this group can access to administrator area.'));
        $ckbAdminPanel->configure('CHECKED',FALSE);
          if($group->adminPanel == 1)
              $ckbAdminPanel->configure('CHECKED',TRUE);
        $form->add($ckbAdminPanel);

        $btnEditeGroup = new control\button('btnEditeGroup');
        $btnEditeGroup->configure('LABEL',_('New Group'));
        $btnEditeGroup->configure('TYPE','primary');
        $btnEditeGroup->p_onclick_plugin = 'users';
        $btnEditeGroup->p_onclick_function = 'btnOnclickEditeGroup';

        $btn_cancel = new control\button('btn_cancel');
        $btn_cancel->configure('LABEL',_('Cancel'));
        $btn_cancel->configure('HREF',core\general::createUrl(['service','administrator','load','users','listGroups']));

        $row = new control\row;
        $row->configure('IN_TABLE',false);

        $row->add($btnEditeGroup,1);
        $row->add($btn_cancel,11);
        $form->add($row);
        return [sprintf(_('Edite %s group'),$group->name),$form->draw()];
    }
}
