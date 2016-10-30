<?php
namespace Mega\Apps\Administrator;
use \Mega\Control as control;
use \Mega\Cls\browser as browser;
use \Mega\Cls\template as template;
use \Mega\Cls\core as core;

class view {
	
	/*
	 * construct
	 */
	function __construct(){
		
	}
	
	/*
	 * load basic administrator panel
	 * @param array(2) $content, (0=>title, 1=>content)
	 * @param array $user, user info
	 * @param array $settings, plugin settings
	 * @return string, html content
	 */
	protected function viewLoad($menus,$content,$user,$settings){
		
		$raintpl = template\raintpl::singleton();
		//configure raintpl //
		$raintpl->configure('tpl_dir', APP_PATH . '/mega/apps/administrator/tpl/');
		
		browser\page::addHeader('<link href="' . DOMAIN_EXE . '/mega/apps/administrator/style/core_content.css" rel="stylesheet">');
		$localize = core\localize::singleton();
		$local = $localize->localize();
		if($local->direction == 'RTL'){
			browser\page::addHeader('<link href="' . DOMAIN_EXE . '/mega/apps/administrator/style/rtl_core_content.css" rel="stylesheet">');
		}
		//Assign variables
		$raintpl->assign( "menu", $menus);
		$raintpl->assign( "content", $content[1]);
		$raintpl->assign( "title", $content[0]);
		$raintpl->assign( "user_logout", _t('Log Out')	);
		$raintpl->assign( "user_logout_url", core\general::createUrl(['users','logout']	));
		$raintpl->assign( "user_name", $user->username);
		$raintpl->assign( "powered_by", _t('Powered by MegaCMF')	);
		$raintpl->assign( "view_site", _t('View website')	);
		$raintpl->assign( "view_site_url",DOMAIN_EXE);
		$raintpl->assign( "dashboard", _t('Dashboard')	);
		$raintpl->assign( "user_profile", _t('Profile'));
		$raintpl->assign( "core_version", sprintf(_t('Version:%s'),$settings->core_version));
		$raintpl->assign( "build_num", sprintf(_t('Build Number:%s'),$settings->build_num));
		$raintpl->assign( "user_profile_url", core\general::createUrl(array('users/profile')));
	
		$raintpl->assign( "sarkesh_admin", _t('Sarkesh Administrator')	);
		$raintpl->assign( "sarkesh_admin_url", core\general::createUrl(array('service', 'administrator','load','administrator','dashboard')	));
		
		return  browser\page::simplePage($content[0],$raintpl->draw('core_content', true ));

	}
	
	/*
	 * show dashboard administrator form
	 * @return string, html content
	 */
	public function viewDashboard(){
		$raintpl = template\raintpl::singleton();
		//configure raintpl //
		$raintpl->configure('tpl_dir', APP_PATH . '/mega/apps/administrator/tpl/');
		
		//Assign variables
		$raintpl->assign( "BasicSettings", _t('Basic Settings'));
		$raintpl->assign( "RegionalandLanguages", _t('Regional and Languages'));
		$raintpl->assign( "Appearance", _t('Appearance'));
		$raintpl->assign( "Plugins",_t('Plugins'));
		$raintpl->assign( "Blocks", _t('Blocks'));
		$raintpl->assign( "Usersandpermissions", _t('Users and permissions'));
		$raintpl->assign( "url_regional", core\general::createUrl(['service','administrator','load','administrator','regAndLang']));
		
		$raintpl->assign( "system_update", _t('Update center'));
		$raintpl->assign( "url_system_update", core\general::createUrl(['service','administrator','load','administrator','checkUpdate']));

		$raintpl->assign( "blog_new_post", _t('New page'));
		$raintpl->assign( "url_blog_new_post", core\general::createUrl(['service','administrator','load','page','newPage']));

		$raintpl->assign( "core_settings", _t('Core settings'));
		$raintpl->assign( "url_core_settings", core\general::createUrl(['service','administrator','load','administrator','coreSettings']));

		$raintpl->assign( "url_appearance", core\general::createUrl(['service','administrator','load','administrator','themes']));
		$raintpl->assign( "url_plugins", core\general::createUrl(['service','administrator','load','administrator','plugins']));
		
		$raintpl->assign( "url_blocks", core\general::createUrl(['service','administrator','load','administrator','blocks']));
		$raintpl->assign( "url_uap",core\general::createUrl(['service','administrator','load','users','listPeople']));
		
		$raintpl->assign( "url_basic", core\general::createUrl(['service','administrator','load','administrator','basicSettings']));
		$raintpl->assign( "site", DOMAIN_EXE);
		
		//draw and return back content
		return array(_t('Dashboard'),$raintpl->draw('core_dashboard', true )	);
	}
	
	/*
	 * check for updates
	 * @param string $siteBuildNumber, site build number
	 * @param string $lastBuildNumber, last published build number
	 * @return string, html content
	 */
	public function viewCheckUpdate($siteBuildNumber,$lastBuildNumber){
		$form = new control\form('frmAdministratorCheckupdates');
		$logo = new control\image('imgLogo');
		$logo->src = DOMAIN_EXE . '/Etc/images/sarkesh_128.png';
		$form->add($logo);
		
		$lblCurrent = new control\label;
		$lblCurrent->label = sprintf(_t('Your system build number:%s'),$siteBuildNumber);
		
		$lblLast = new control\label;
		$lblLast->label = sprintf(_t('Last relased build number:%s'),$lastBuildNumber);
		$form->add($lblCurrent);
		$form->add($lblLast);

		//show update message
		$lblUpdateMsg = new control\label;
		$lblUpdateMsg->label = _t('Your system is update! noting to do.');
		if($siteBuildNumber<$lastBuildNumber){
			$lastBuild = new control\hidden('lastBuild');
			$lastBuild->value = $lastBuildNumber;
			$form->add($lastBuild);
			
			$row = new control\row();
			$btnUpdate = new control\button('btnUpdate');
			$btnUpdate->label = _t('Update to new version');
			$btnUpdate->p_onclick_plugin = 'administrator';
			$btnUpdate->p_onclick_function = 'updateSystem';
			$btnUpdate->type = 'success';
			$row->add($btnUpdate);
			
			$lblUpdateMsg->label = sprintf(_t('Your system is out of date.for get latest update go to sarkesh website'),$lastBuildNumber);
			$btnJump = new control\button('btnUpdate');
			$btnJump->label = _t('Jump to release notes');
			$btnJump->href = S_SERVER_INFO . 'release_notes.txt';
			$form->add($lblUpdateMsg);
			$row->add($btnJump);
			$form->add($row);
		}
		else{
			$form->add($lblUpdateMsg);
		}
		return [_t('Updates'),$form->draw()];
	}
	
	/*
	 * function for show core_settings
	 * @param object $settings, administrator settings
	 * @return string html content
	 */

	protected function viewCoreSettings($settings){
		$form = new control\form('administrator_core_settings');
		//enable clean url
		$ckbCleanUrl = new control\checkbox('ckbCleanUrl');
		$ckbCleanUrl->configure('LABEL',_t('Enable clean url') );
		$ckbCleanUrl->configure('CHECKED', FALSE);
		$ckbCleanUrl->configure('HELP',_t('With this option,pages with parameters will be replaced with clean address.'));
		if($settings->cleanUrl == 1){
			$ckbCleanUrl->configure('CHECKED',TRUE);
		}
		$form->add($ckbCleanUrl);

        //develpers mode
        $ckbDevMode = new control\checkbox('ckbDevMode');
        $ckbDevMode->configure('LABEL',_t('Developers mode?') );
        $ckbDevMode->configure('CHECKED', FALSE);
        $ckbDevMode->configure('HELP',_t('If you mark this options,all errors that occurred in system will be send to SarkeshCMF developers. '));
        if($settings->devMode == 1){
            $ckbDevMode->configure('CHECKED',TRUE);
        }
        $form->add($ckbDevMode);

		$txtValidateTime = new control\textbox('txtValidateTime');
		$txtValidateTime->label = _t('Validation Expire time:');
		$txtValidateTime->help = _t('Thos option set external variables expire time.');
		$txtValidateTime->value = $settings->validator_max_time / 3600;
		$txtValidateTime->size = 3;
		$txtValidateTime->addon = _t('Hour(s)');
		$form->add($txtValidateTime);
		
		//add update and cancel buttons
		$btnUpdate = new control\button('btnUpdate');
		$btnUpdate->configure('LABEL',_t('Update'));
		$btnUpdate->configure('P_ONCLICK_PLUGIN','administrator');
		$btnUpdate->configure('P_ONCLICK_FUNCTION','onclickCoreSettings');
		$btnUpdate->configure('TYPE','primary');
		
		$btnCancel = new control\button('btnCancel');
		$btnCancel->configure('LABEL',_t('Cancel'));
		$btnCancel->configure('HREF',core\general::createUrl(array('service', 'administrator','load','administrator','dashboard')	));
		
		$row = new control\row;
		$row->configure('IN_TABLE',false);
		
		$row->add($btnUpdate,1);
		$row->add($btnCancel,11);
		$form->add($row);  
		return [_t('Core settings'),$form->draw()];
	}
	
	//show list of localize for edite basic settings
    protected function viewBasicSettings($locals){
    	$form = new control\form('admin_list_basic_settings');
		$table = new control\table('admin_list_basic_settings');
		$counter = 0;
		foreach($locals as $key=>$local){
			$counter += 1;
			$row = new control\row('content_catalogue_row');
			
			$lblID = new control\label('lbl');
			$lblID->configure('LABEL',$counter);
			$row->add($lblID,1);
			
			$lbl_cat = new control\label('lbl');
			$lbl_cat->configure('LABEL',$local->language_name);
			$row->add($lbl_cat,1);
			
			$btnEditeLocal = new control\button('btnEditeLocal');
			$btnEditeLocal->configure('LABEL',_t('Edite'));
			$btnEditeLocal->configure('HREF',core\general::createUrl(['service','administrator','load','administrator','basicSettingsEdite',$local->id]));
			$row->add($btnEditeLocal,2);
			if($local->can_delete != '0'){
				$btnDelete = new control\button('btnDelete');
				$btnDelete->configure('LABEL',_t('Delete'));
				$btnDelete->configure('HREF',core\general::createUrl(['service','administrator','load','administrator','sureDeleteLocal',$local->id]));
				$btnDelete->configure('TYPE','danger');
				$row->add($btnDelete,2);
			}
			
			
			$table->add_row($row);
			$table->configure('HEADERS',[_t('ID'),_t('Name'),_t('Edit'),_t('Delete')]);
			$table->configure('HEADERS_WIDTH',[1,9,1,1]);
			$table->configure('ALIGN_CENTER',[TRUE,FALSE,TRUE,TRUE]);
			$table->configure('BORDER',true);
		}
		
		$form->add($table);
		
		return [_t('Locals'),$form->draw()];
    }
	 //this function show blocks for that user can manage widgets
    public function viewBlocks($blocks,$places){
		
		$form = new control\form("core_manage_blocks");
		$form->configure('LABEL',_t('Blocks'));
		$table = new control\table;
		$counter = 0;
		foreach($blocks as $key=>$block){
			$counter ++ ;
			$row = new control\row;
			
			//add id to table for count rows
			$lblID = new control\label($counter);
			$row->add($lblID,1);
			
			//add block name
			$lblBlockName = new control\label($block->block_name);
			$row->add($lblBlockName,2);
					
			//add plugin state			
			if($block->position == 'off'){
				//block is disabled
				$lblBlockState = new control\label(_t('Off'));

			}
			else{
				//show position
				$lblBlockState = new control\label($block->position);
			}
			$row->add($lblBlockState,1);
			
			//add rank of block
			$lbl_block_rank = new control\label($block->rank);
			$row->add($lbl_block_rank,2);

			$btnEdite = new control\button('btnEdite');
			$btnEdite->configure('LABEL',_t('Edite'));
			$btnEdite->configure('HREF',core\general::createUrl(['service','administrator','load','administrator','editeBlock',$block->id]));
			$btnEdite->configure('TYPE','primary');
			$row->add($btnEdite,1);
			if($block->visual == '1' && $block->handel == 'staticBlock' && $block->name == 'administrator'){
				//add delete button
				$btn_sure_delete_block = new control\button('btn_sure_delete_block');
				$btn_sure_delete_block->configure('LABEL',_t('Delete'));
				$btn_sure_delete_block->configure('HREF',core\general::createUrl(['service','administrator','load','administrator','sureDeleteBlock',$block->id]));
				$btn_sure_delete_block->configure('TYPE','danger');
				$row->add($btn_sure_delete_block,1);

				//add edite static values
				$btnEdite_static_block = new control\button('btnEdite_static_block');
				$btnEdite_static_block->configure('LABEL',_t('Edite static'));
				$btnEdite_static_block->configure('HREF',core\general::createUrl(['service','administrator','load','administrator','editeStaticBlock',$block->id]));
				$btnEdite_static_block->configure('TYPE','default');
				$row->add($btnEdite_static_block,1);
			}
			
			//ADD ROW TO TABLE
			$table->add_row($row);
		}
		
		
		//add headers to table
		$table->configure('HEADERS',array(_t('ID'),_t('Name'),_t('Place'),_t('Rank'),_t('Edite'),_t('Delete'),_t('Static')));
		$table->configure('HEADERS_WIDTH',[1,3,1,1,1,1,1]);
		$table->configure('ALIGN_CENTER',[TRUE,FALSE,TRUE,TRUE,TRUE,TRUE,TRUE]);
		$table->configure('BORDER',true);
		$form->add($table);
	
		//add insert html block and cancel buttons
		$row = new control\row;
		$btn_static_block = new control\button('btn_static_block');
		$btn_static_block->configure('LABEL',_t('Add static block'));
		$btn_static_block->configure('HREF',core\general::createUrl(['service','administrator','load','administrator','newStaticBlock']));
		$btn_static_block->configure('TYPE','primary');
		$row->add($btn_static_block,2);
		//add cancel buttons
		$btnCancel = new control\button('btnCancel');
		$btnCancel->configure('LABEL',_t('Cancel'));
		$btnCancel->configure('HREF',core\general::createUrl(['service','administrator','load','administrator','dashboard']));
		$row->add($btnCancel,1);

		$form->add($row); 
		
		return array(_t('Manage Blocks'),$form->draw());
	}
	
	/*
	 * edite localize settings
	 * @param object $local, selected local info from database
	 * @return string, html content
	 */
	 protected function viewBasicSettingsEdite($local){
		$form = new control\form('administrator_basic_settings_edite');
        
        $hid_id = new control\hidden('hidID');
        $hid_id->configure('VALUE',$local->id);
        $form->add($hid_id);
        
        $txtSiteName = new control\textbox('txtName');
        $txtSiteName->configure('LABEL',_t('Site name'));
        $txtSiteName->configure('VALUE',$local->name);
        $txtSiteName->configure('ADDON','*');
        $txtSiteName->configure('SIZE',3);
        $form->add($txtSiteName);
        
        $txtSlogan = new control\textbox('txtSlogan');
        $txtSlogan->configure('LABEL',_t('Slogan'));
        $txtSlogan->configure('VALUE',$local->slogan);
        $txtSlogan->configure('HELP',_t("How this is used depends on your site's theme."));
        $txtSlogan->configure('ADDON','O'); //O -> OPTIONAL
        $txtSlogan->configure('SIZE',3);
        $form->add($txtSlogan);
        
        $txtEmail = new control\textbox('txtEmail');
        $txtEmail->configure('LABEL',_t('Email address'));
        $txtEmail->configure('VALUE',$local->email);
        $txtEmail->configure('ADDON','*');
        $txtEmail->configure('SIZE',5);
        $txtEmail->configure('HELP',_t("The From address in automated e-mails sent during registration and new password requests, and other notifications. (Use an address ending in your site's domain to help prevent this e-mail being flagged as spam.)"));
        $form->add($txtEmail);
        
        $txtHome = new control\textbox('txtHome');
        $txtHome->configure('LABEL',_t('Front page'));
        $txtHome->configure('VALUE',$local->home);
        $txtHome->configure('ADDON',DOMAIN_EXE . '/');
        $txtHome->configure('SIZE',5);
        $txtHome->configure('HELP',_t("Optionally, specify a relative URL to display as the front page. be careful for that this address be correct!"));
        $form->add($txtHome);

        //add description to head of page
        $txtDes = new control\textarea('txtDes');
        $txtDes->configure('EDITOR',FALSE);
        $txtDes->configure('VALUE',$local->header_tags);
        $txtDes->configure('LABEL',_t('Description'));
        $txtDes->configure('HELP',_t('your text show in header of page for use in search engines.'));
        $txtDes->configure('EDITOR',FALSE);
        $txtDes->configure('ROWS',5);
		$txtDes->configure('SIZE',7);
        $form->add($txtDes);
        
        //add update and cancel buttons
		$btnUpdate = new control\button('btnUpdate');
		$btnUpdate->configure('LABEL',_t('Update'));
		$btnUpdate->configure('P_ONCLICK_PLUGIN','administrator');
		$btnUpdate->configure('P_ONCLICK_FUNCTION','onclickBtnBasicSettingsEdite');
		$btnUpdate->configure('TYPE','primary');

		$btnCancel = new control\button('btnCancel');
		$btnCancel->configure('LABEL',_t('Cancel'));
		$btnCancel->configure('HREF',core\general::createUrl(['service','administrator','load','administrator','basicSettings']));
		
		$row = new control\row;
		$row->configure('IN_TABLE',false);
		
		$row->add($btnUpdate,1);
		$row->add($btnCancel,11);
		$form->add($row);                
        
        
        return [_t('General Settings'),$form->draw()];
	 }
	 
	 /*
	 * show form for add new static block
	 * @param object block info, default null
	 * @return string, html content
	 */
	protected function viewNewStaticBlock($block=null){
		$form = new control\form('administrator_add_static_block');

		$txt_block_name = new control\textbox('txtName');
        $txt_block_name->configure('LABEL',_t('Block name'));
        $txt_block_name->configure('ADDON','*');
        $txt_block_name->configure('SIZE',5);
        $txt_block_name->configure('HELP',_t("Block name not show in template"));

        $txt_block_label = new control\textbox('txtLabel');
        $txt_block_label->configure('LABEL',_t('Block label'));
        $txt_block_label->configure('ADDON','O');
        $txt_block_label->configure('SIZE',5);
        $txt_block_label->configure('HELP',_t("if enable show header option this label will be show."));

        $ckb_show_header = new control\checkbox('ckbShowHeader');
		$ckb_show_header->configure('LABEL',_t('Show header') );
		$ckb_show_header->configure('CHECKED', FALSE);
		$ckb_show_header->configure('HELP',_t('If this option checked,label of block will showed at the top of block.'));

		$txt_content = new control\textarea('txtContent');
		$txt_content->configure('LABEL',_t('content of block'));
		$txt_content->configure('EDITOR',FALSE);
		$txt_content->configure('SIZE',7);
		$txt_content->configure('ROWS',7);
		$txt_content->configure('HELP',_t('All of content that you will enter,show in block.you can use html and javascript codes and style with CSS.'));

		//add btn_add and cancel buttons
		$btn_do = new control\button('btnAddBlock');
		$btn_do->configure('LABEL',_t('Add block'));
		$btn_do->configure('P_ONCLICK_PLUGIN','administrator');
		$btn_do->configure('P_ONCLICK_FUNCTION','onclickBtnDoBlock');
		$btn_do->configure('TYPE','primary');
		
		$btnCancel = new control\button('btnCancel');
		$btnCancel->configure('LABEL',_t('Cancel'));
		$btnCancel->configure('HREF',core\general::createUrl(['service','administrator','load','administrator','blocks']));
		
		$row = new control\row;
		$row->configure('IN_TABLE',false);
		//check for edite mode
		$header = _t('New static block');
		if(!is_null($block)){
			$header = _t('Update static block');
			
			$hid_id = new control\hidden('hidID');
			$hid_id->configure('VALUE',$block->id);
			$form->add($hid_id);

			$txt_block_name->configure('VALUE',$block->name);
			$value = explode('<::::>',$block->value);
			$txt_block_label->configure('VALUE',$value[0]);
			$txt_content->configure('VALUE',$value[1]);
			if($block->show_header == '1'){
				$ckb_show_header->configure('CHECKED', TRUE);
			}

			$btn_do->configure('LABEL',_t('Edite block'));
		}
 		$form->addArray([$txt_block_name,$txt_block_label,$ckb_show_header,$txt_content]);
		$row->add($btn_do,1);
		$row->add($btnCancel,11);
		$form->add($row); 
		return [$header,$form->draw()];	
	}
	
	/*
	 * show edite block form
	 * @param object $block, block information
	 * @param array $places, theme defined places
	 * @param array $locals, all localize properties
	 * @return array page html content
	 */
	protected function viewEditeBlock($block,$places,$locals){
		$form = new control\form("administratorEditeBlock");
		
		//Create hidden id of block
		$hidID = new control\hidden('hidID');
        $hidID->configure('VALUE',$block->id);
        $form->add($hidID);
        
		//create combobox for ranking
		$cobRank = new control\combobox('cobRank');
        $cobRank->configure('LABEL',_t('Rank'));
        $cobRank->configure('SELECTED_INDEX',$block->rank);
        $cobRank->configure('SOURCE',[0,1,2,3,4,5,6,7,8,9]);
        $cobRank->configure('SIZE',3);
		$form->add($cobRank);

		$ckbShowHeader = new control\checkbox('ckbShowHeader');
		$ckbShowHeader->configure('LABEL',_t('Show header') );
		$ckbShowHeader->configure('CHECKED', FALSE);
		if($block->show_header == '1'){
			$ckbShowHeader->configure('CHECKED', TRUE);
		}
		$ckbShowHeader->configure('HELP',_t('If this option checked,label of block will showed at the top of block.'));
		$form->add($ckbShowHeader);

		//create textarea for pages
		$txtPages = new control\textarea('txtPages');
		$txtPages->configure('EDITOR',FALSE);
		$txtPages->configure('VALUE',$block->pages);
		$txtPages->configure('LABEL',_t('Pages'));
		$txtPages->configure('HELP',_t('Use \',\' for seperate page urls. for use home page enter \'frontpage\' and start your internal urls with \'\\\'.'));
		$txtPages->configure('ROWS',5);
		$txtPages->configure('SIZE',7);
		$form->add($txtPages);
		
		//ADD RADIO BUTTON FOR SELECT PAGES
		$radBot = new control\radiobuttons('rad_show_option');
		$radBot->configure('LABEL',_t('With this option you can select pages for show.'));
		$raditAllPages = new control\radioitem('radItAllow');
		$raditAllPages->configure('LABEL',_t('All pages espect that comes above.'));
		if($block->pages_ad == '1') $raditAllPages->configure('CHECKED',TRUE);
		$radBot->add($raditAllPages);
		
		$raditExPages = new control\radioitem('rad_it_deny');
		$raditExPages->configure('LABEL',_t('show in pages that comes above.'));
		if($block->pages_ad == '0') $raditExPages->configure('CHECKED',TRUE);
		$radBot->add($raditExPages);
		$form->add($radBot);

		//add localize for show block
		$cobLocalize = new control\combobox('cobLanguage');
		$cobLocalize->configure('LABEL',_t('Localize block'));
		$cobLocalize->configure('HELP',_t('block will showed in selected localize.'));
		$cobLocalize->configure('SIZE',4);
		$cobLocalize->configure('SELECTED_INDEX',$block->localize);
		$cobLocalize->configure('SOURCE',$locals);
		
		$form->add($cobLocalize);

		//create combobox for positions
		$cobPosiotion = new control\combobox('cobPosition');
        $cobPosiotion->configure('LABEL',_t('Position'));
        $cobPosiotion->configure('SELECTED_INDEX',$block->position);
        $cobPosiotion->configure('SOURCE',$places);
        $cobPosiotion->configure('SIZE',3);
		$form->add($cobPosiotion);
		
		
		//add update and cancel buttons
		$btnUpdate = new control\button('btnUpdate');
		$btnUpdate->configure('LABEL',_t('Update'));
		$btnUpdate->configure('P_ONCLICK_PLUGIN','administrator');
		$btnUpdate->configure('P_ONCLICK_FUNCTION','onclickBtnUpdateBlock');
		$btnUpdate->configure('TYPE','primary');
		
		$btnCancel = new control\button('btnCancel');
		$btnCancel->configure('LABEL',_t('Cancel'));
		$btnCancel->configure('HREF',core\general::createUrl(['service','1','plugin','administrator','action','main','p','administrator','a','blocks']));
		
		$row = new control\row;
		$row->configure('IN_TABLE',false);
		
		$row->add($btnUpdate,1);
		$row->add($btnCancel,11);
		$form->add($row);   
		
		
		return [_t('Edite Block:').$block->name,$form->draw()];
	}
	
	/*
	 * show delete message
	 * @param object $block, block information
	 * @return string, html content
	 */
	public function viewSureDeleteBlock($block){
		$form = new control\form('administartor_sure_delete_blocks');

		$hidID = new control\hidden('hidID');
		$hidID->configure('VALUE',$block->id);

		$lblMsg = new control\label;
		$lblMsg->configure('LABEL',sprintf(_t('Are you sure for delete %s ?'),$block->name));
	
		$btnDelete = new control\button('btnDelete');
		$btnDelete->configure('LABEL',_t('Delete'));
		$btnDelete->configure('TYPE','danger');
		$btnDelete->configure('P_ONCLICK_PLUGIN','administrator');
		$btnDelete->configure('P_ONCLICK_FUNCTION','onclickBtnDeleteBlock');
		
		$btnCancel = new control\button('btnCancel');
		$btnCancel->configure('LABEL',_t('Cancel'));
		$btnCancel->configure('HREF',core\general::createUrl(['service','administrator','load','administrator','blocks']));
		
		$row = new control\row;
		$row->configure('IN_TABLE',false);
		
		$row->add($btnDelete,1);
		$row->add($btnCancel,11);

		$form->addArray([$hidID,$lblMsg,$row]);
		return [_t('Delete Static block'),$form->draw()];
	}
	
	/*
	 * show form for manage plugins
	 * $param array $plugins, all plugins options
	 * @return array, html content
	 */
	public function viewPlugins($plugins){
		$form = new control\form("core_manage_plugins");
		$form->configure('LABEL',_t('Plugins'));
		$table = new control\table;
		$counter = 0;
		foreach($plugins as $key=>$plugin){
			$counter ++ ;
			$row = new control\row;
			
			//add id to table for count rows
			$lblID = new control\label($counter);
			$row->add($lblID,1);
			
			//add plugin name
			$lblPluginName = new control\label($plugin->name);
			$row->add($lblPluginName,2);
			$btnActive = new control\button;		
			//add plugin state			
			if($plugin->enable != 1){
				$btnActive->configure('LABEL',_t('Active'));
				$btnActive->configure('TYPE','success');	
			}
			else{
				$btnActive->configure('LABEL',_t('Disactive'));
				$btnActive->configure('TYPE','danger');			
			}
			$btnActive->configure('VALUE',$plugin->id);
			$btnActive->configure('P_ONCLICK_PLUGIN','administrator');
			$btnActive->configure('P_ONCLICK_FUNCTION','onclickBtnChangePlugin');
			$row->add($btnActive,1);

            $btnReinstall = new control\button('btnReinstall');
            $btnReinstall->label = _t('Reinstall');
            $btnReinstall->configure('VALUE',$plugin->id);
            $btnReinstall->configure('P_ONCLICK_PLUGIN','administrator');
            $btnReinstall->configure('P_ONCLICK_FUNCTION','onclickBtnReinstallPlugin');
            $row->add($btnReinstall,1);

			$table->add_row($row);
			
		}

		//add headers to table
		$table->configure('HEADERS',array(_t('ID'),_t('Name'),_t('Options'),_t('Reinstall')));
		$table->configure('HEADERS_WIDTH',[1,5,2,2]);
		$table->configure('ALIGN_CENTER',[TRUE,FALSE,TRUE,TRUE]);
		$table->configure('BORDER',true);
		$form->add($table);

        $lblNote = new control\label(_t('After reinstall plugins that plugin will be disactive and you should active it again'));
        $lblNote->bs_control = true;
        $lblNote->type = 'warning';
        $form->add($lblNote);

		return array(_t('Plugins'),$form->draw());
	}
	
	/*
	 * show form for manage themes
	 * @param array $themes ,theme names
	 * @param string $activeTheme, current active theme
	 * @return string, html content
	 */
	public function viewThemes($themes,$activeTheme){
		$form = new control\form("core_manage_themes");
		$form->configure('LABEL',_t('Themes'));
		$table = new control\table;
		
		foreach($themes as $key=>$theme){
			$themeInfo = call_user_func(['themes\\' . $theme,'getInfo']);
			$row = new control\row;
			
			//add id to table for count rows
			$lblID = new control\label($key+1);
			$row->add($lblID,1);
			
			//add theme name
			$lblThemeName = new control\label($themeInfo->name);
			$row->add($lblThemeName,2);
			
			//add author of theme
			$lblAuthor = new control\label($themeInfo->author);
			$row->add($lblAuthor,2);

			//add active theme button
			if($theme !== $activeTheme){
				$btnActive = new control\button;
				$btnActive->configure('LABEL',_t('Active this'));
				$btnActive->configure('TYPE','success');
				$btnActive->configure('VALUE',$theme);
				$btnActive->configure('P_ONCLICK_PLUGIN','administrator');
				$btnActive->configure('P_ONCLICK_FUNCTION','onclickBtnChangeTheme');
				$row->add($btnActive,1);
			}
            
			$table->add_row($row);
			
		}
		
		//add headers to table
		$table->configure('HEADERS',array(_t('ID'),_t('Name'),_t('Author'),_t('Options')));
		$table->configure('HEADERS_WIDTH',[1,5,3,3]);
		$table->configure('ALIGN_CENTER',[TRUE,FALSE,FALSE,TRUE]);
		$table->configure('BORDER',true);
		$form->add($table);
		return array(_t('Appearance'),$form->draw());
	}
	
		/*
	 * show form for manage themes
	 * @return string, html content
	 */
	public function viewRegAndLang($countries,$timezones,$locals,$local){
		 $form = new control\form('administrator_regandlang_settings');
        
        //show default countries
        $cobCountries = new control\combobox('cobContries');
        $cobCountries->configure('LABEL',_t('Default country'));
        $cobCountries->configure('TABLE',$countries);
        $cobCountries->configure('COLUMN_VALUES','country_name');
        $cobCountries->configure('COLUMN_LABELS','country_name');
        $cobCountries->configure('SIZE',3);
        $form->add($cobCountries);
        
        //default language
        $cobLanguage = new control\combobox('cobLanguage');
        $cobLanguage->configure('LABEL',_t('Default Localize'));
        $cobLanguage->configure('TABLE',$locals);
        $cobLanguage->configure('SELECTED_INDEX',$local->id);
        $cobLanguage->configure('COLUMN_VALUES','id');
        $cobLanguage->configure('COLUMN_LABELS','language_name');
        $cobLanguage->configure('SIZE',3);
        $form->add($cobLanguage);
         
        //show default timezones
        $cobTimezone = new control\combobox('cobTimezones');
        $cobTimezone->configure('LABEL',_t('Default Timezone'));
        $cobTimezone->configure('TABLE',$timezones);
        $cobTimezone->configure('COLUMN_VALUES','timezone_name');
        $cobTimezone->configure('COLUMN_LABELS','timezone_name');
        $cobTimezone->configure('SIZE',3);
        $form->add($cobTimezone);
        
        //add update and cancel buttons
		$btnUpdate = new control\button('btnUpdate');
		$btnUpdate->configure('LABEL',_t('Update'));
		$btnUpdate->configure('P_ONCLICK_PLUGIN','administrator');
		$btnUpdate->configure('P_ONCLICK_FUNCTION','onclickBtnUpdateRegandlang');
		$btnUpdate->configure('TYPE','primary');
		
		$btnCancel = new control\button('btnCancel');
		$btnCancel->configure('LABEL',_t('Cancel'));
		$btnCancel->configure('HREF',core\general::createUrl(['service','administrator','load','administrator','dashboard']));
		
		$row = new control\row;
		$row->configure('IN_TABLE',false);
		
		$row->add($btnUpdate,1);
		$row->add($btnCancel,11);
		$form->add($row);   
        
        return[_t('Regional and languages'),$form->draw()];
	}
	
}
