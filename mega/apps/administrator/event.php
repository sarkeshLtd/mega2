<?php
namespace Mega\Apps\Administrator;
use \Mega\Control as control;
use \Mega\Cls\core as core;
use \Mega\Cls\browser as browser;
use \Mega\Cls\Database as db;
use \core\data as data;

class event{
	use addons;
	
	function __construct(){
	}
	
	 /*
	 * save core settings
	 * @param array $e,form properties
	 * @return array $e,form properties
	 */
	public function onclickCoreSettings($e){
		if($this->hasAdminPanel()){
			$registry = core\registry::singleton();
            //save clean url
			$val = 0;
			if($e['ckbCleanUrl']['CHECKED'] == 1)
				$val = 1;
			$registry->set('administrator','cleanUrl',$val);
            //save developers mode
            $val = 0;
            if($e['ckbDevMode']['CHECKED'] == 1)
                $val = 1;
            $registry->set('administrator','devMode',$val);
			if(data\type::isNumber($e['txtValidateTime']['VALUE'])){
				$registry->set('administrator','validator_max_time',$e['txtValidateTime']['VALUE']*3600);
				$registry->set('administrator','cookie_max_time',$e['txtValidateTime']['VALUE']*3600);
			}
			else{
				$e['txtValidateTime']['VALUE'] = '';
				return browser\msg::modal($e,_('Data type error'),_('Validator expire time should be numberic.all settings expect this was saved.'),'warning');
			}
			return browser\msg::modalSuccessfull($e,'N');
		}
		return browser\msg::modalNoPermission($e);
	 }
	 
	 /*
	 * save core settings
	 * @param array $e,form properties
	 * @return array $e,form properties
	 */
	public function onclickBtnBasicSettingsEdite($e){
		if($this->hasAdminPanel()){
			if(array_key_exists('hidID',$e)){
				$orm = db\orm::singleton();
				if($orm->count('localize','id=?',[$e['hidID']['VALUE']]) == 1){
					$local = $orm->findOne('localize','id=?',[$e['hidID']['VALUE']]);
					$local->name = $e['txtName']['VALUE'];
					$local->slogan = $e['txtSlogan']['VALUE'];
					$local->email = $e['txtEmail']['VALUE'];
					$local->home = $e['txtHome']['VALUE'];
					$local->header_tags = $e['txtDes']['VALUE'];
					$orm->store($local);
					return browser\msg::modalSuccessfull($e,'N');
				}
				return browser\msg::modalEventError($e);
			}
		}
		return browser\msg::modalNoPermission($e);
	}
	
	/*
	 * insert or update static block
	 * @param array $e,form properties
	 * @return array $e,form properties
	 */
	public function onclickBtnDoBlock($e){
		if($this->hasAdminPanel()){
			if($e['txtContent']['VALUE'] != ''){	
				$orm = db\orm::singleton();
				$plugin = $orm->findOne('plugins','name=?',['administrator']);
				$block = $orm->dispense('blocks');
				if(array_key_exists('hidID', $e)) $block = $orm->load('blocks',$e['hidID']['VALUE']);
				else {
					$block = $orm->dispense('blocks');
					$block->position = 'Off';
				}
				$adminPlugin = $orm->findOne('plugins','name=?',['administrator']);
				$block->name = $e['txtName']['VALUE'];
				$block->plugin = $adminPlugin->id;
				$block->value = $e['txtLabel']['VALUE'] . '<::::>' . $e['txtContent']['VALUE'];
				$block->visual = 1;
				$block->localize = 'all';
				$block->handel = 'staticBlock';
				$block->show_header = '0';
				if($e['ckbShowHeader']['CHECKED'] == '1') $block->show_header = '1';
				$orm->store($block);
				return browser\msg::modalSuccessfull($e,['service','administrator','load','administrator','blocks']);
			}
			return browser\msg::modalNotComplete($e);
		}
		return browser\msg::modalNoPermission($e);
	}
	
	/*
	 * insert or update static block
	 * @param array $e,form properties
	 * @return array $e,form properties
	 */
	public function onclickBtnUpdateBlock($e){
		if($this->hasAdminPanel()){
			$orm = db\orm::singleton();
			if($orm->count('blocks','id=?',[$e['hidID']['VALUE']]) != 0){
				$block = $orm->findOne('blocks','id=?',[$e['hidID']['VALUE']]);
				$block->rank = $e['cobRank']['SELECTED'];
				$block->position = $e['cobPosition']['SELECTED'];
				$block->localize = $e['cobLanguage']['SELECTED'];
				$block->pages = $e['txtPages']['VALUE'];
				$block->pages_ad = '0';
				if($e['radItAllow']['CHECKED'] == '1'){
					$block->pages_ad = '1';
				}
				
				//SHOW HEADER SAVE
				$block->show_header = '0';
				if($e['ckbShowHeader']['CHECKED'] == '1'){
					$block->show_header = '1';
				}

				//save changes
				$orm->store($block);
				return browser\msg::modalsuccessfull($e,['service','administrator','load','administrator','blocks']);
				return $e;
			}
		}
		return browser\msg::modalNoPermission($e);
	}
	
	/*
	 * insert or update static block
	 * @param array $e,form properties
	 * @return array $e,form properties
	 */
	public function onclickBtnDeleteBlock($e){
		if($this->hasAdminPanel()){
			$orm = db\orm::singleton();
			if($orm->count('blocks','id=? and visual=?',[$e['hidID']['VALUE'],1]) != 0){
				$orm->exec('DELETE FROM blocks WHERE id=?',[$e['hidID']['VALUE']],NON_SELECT);
				//save changes
				return browser\msg::modalsuccessfull($e,['service','administrator','load','administrator','blocks']);
			}
		}
		return browser\msg::modalNoPermission($e);
		
		
	}
	
	/*
	 * change plugin state
	 * @param array $e,form properties
	 * @return array $e,form properties
	 */
	public function onclickBtnChangePlugin($e){
		if($this->hasAdminPanel()){
			$orm = db\orm::singleton();
			if($orm->count('plugins','id=?',[$e['CLICK']['VALUE']]) != 0){
				$plugin = $orm->findOne('plugins','id=?',[$e['CLICK']['VALUE']]);
				$state = 0;
				if($plugin->enable == 0) $state = 1;
				$plugin->enable = $state;
				$orm->store($plugin);
				//save changes
				return browser\msg::modalsuccessfull($e,['service','administrator','load','administrator','plugins']);
			}
			return browser\msg::modalEventError($e);
		}
		return browser\msg::modalNoPermission($e);
	}
	 
	 
	 /*
	 * change change theme of system
	 * @param array $e,form properties
	 * @return array $e,form properties
	 */
	public function onclickBtnChangeTheme($e){
		if($this->hasAdminPanel()){
			if(file_exists(APP_PATH . '/themes/' . $e['CLICK']['VALUE'] . '/info.php')){
				$registry = core\registry::singleton();
				$registry->set('administrator','active_theme',$e['CLICK']['VALUE']);
				//save changes
				return browser\msg::modalsuccessfull($e,['service','administrator','load','administrator','themes']);
			}
			return browser\msg::modalEventError($e);
		}
		return browser\msg::modalNoPermission($e);
	}
	
	/*
	 * change change of regional and language settings
	 * @param array $e,form properties
	 * @return array $e,form properties
	 */
	public function onclickBtnUpdateRegandlang($e){
		if($this->hasAdminPanel()){
			//save default country
			$registry = core\registry::singleton();
			$registry->set('administrator','default_country',$e['cobContries']['SELECTED']);
			//SAVE DEFAULT TIMEZONE
			$registry->set('administrator','default_timezone',$e['cobTimezones']['SELECTED']);
			
			//save default localize
			//disactive old localize
			$orm = db\orm::singleton();
			$localize = $orm->findOne('localize','main=\'1\'');
			$localize->main = 0;
			$orm->store($localize);
			//active new localize
			$localize = $orm->findOne('localize','id=?',[$e['cobLanguage']['SELECTED']]);
			$localize->main = 1;
			$orm->store($localize);
			return browser\msg::modalsuccessfull($e,['service','administrator','load','administrator','dashboard']);
			
		}
		return browser\msg::modalNoPermission($e);
	}
	
	/*
	 * change change of regional and language settings
	 * @param array $e,form properties
	 * @return array $e,form properties
	 */
	public function updateSystem($e){
		if($this->hasAdminPanel()){
			$newVersionAdr = S_UPDATE_SERVER . 'sarkesh_latest.zip';
			$newFile = file_get_contents($newVersionAdr);
			file_put_contents(APP_PATH . '/upload/UPGRADE/sarkesh_latest.zip',$newFile);
			$zipObj = new \Mega\Cls\archive\zip(APP_PATH . '/upload/UPGRADE/sarkesh_latest.zip');
			$zipObj->extract(APP_PATH);
			//include and run update database
			include_once(APP_PATH . '/install/update.php');
			//update build number
			$registry = core\registry::singleton();
			$registry->set('administrator','build_num',$e['lastBuild']['VALUE']);
			return browser\msg::modalsuccessfull($e,'R');
		}
		return browser\msg::modalNoPermission($e);
	}

    /*
	 * change change of regional and language settings
	 * @param array $e,form properties
	 * @return array $e,form properties
	 */
    public function onclickBtnReinstallPlugin($e){
        if($this->hasAdminPanel()){
            $orm = db\orm::singleton();
            if($orm->count('plugins','id=?',[$e['CLICK']['VALUE']]) != 0)
                if($orm->findOne('plugins','id=?',[$e['CLICK']['VALUE']])->can_edite == 1) {
                    $orm->exec('DELETE FROM plugins WHERE id=?;', [$e['CLICK']['VALUE']], NON_SELECT);
                    return browser\msg::modalsuccessfull($e, 'R');
                }
            return browser\msg::modalEventError($e);
        }
        return browser\msg::modalNoPermission($e);
    }
}
