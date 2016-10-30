<?php
namespace Mega\Control\uploader;
use \Mega\Cls\template as template;
use \Mega\Cls\browser as browser;
use \Mega\Cls\core as core;
class view{
	
	private $raintpl;
	private $page;
	
	/*
	 * construct
	 */
	function __construct(){
		$this->raintpl = new template\raintpl;
		$this->page = new browser\page;
	}
	
	//this function draw control
	protected function view_draw($config){
		//configure raintpl //
		$this->raintpl->configure('tpl_dir','mega/control/uploader/');
		
		//add headers to page//
		browser\page::addHeader('<script src="' . DOMAIN_EXE . '/Etc/scripts/events/functions.js"></script>');
		browser\page::addHeader('<script src="' . DOMAIN_EXE . '/mega/control/uploader/ctr_uploader.js"></script>');
		browser\page::addHeader('<link rel="stylesheet" type="text/css" href="' . DOMAIN_EXE . '/mega/control/uploader/ctr_uploader.css" />');
		
		if($config['SCRIPT_SRC'] != ''){browser\page::addHeader('<script src="' . $config['SCRIPT_SRC'] . '"></script>'); }		
		if($config['CSS_FILE'] != ''){ browser\page::addHeader('<link rel="stylesheet" type="text/css" href="' . $config['CSS_FILE']) . '" />';}

		$this->raintpl->assign( "uploadUrl", core\general::createUrl(['service','files','doUpload']));
		$this->raintpl->assign( "removeUrl", core\general::createUrl(['service','files','removeFile']));
		$this->raintpl->assign( "filePort", $config['FORM'] . $config['NAME']);
		$this->raintpl->assign( "form", $config['FORM']);
		$this->raintpl->assign( "name", $config['NAME']);
		$this->raintpl->assign( "label", $config['LABEL']);
		$this->raintpl->assign( "help", $config['HELP']);
		$this->raintpl->assign( "strSelect", _t('Select file'));
		$this->raintpl->assign( "strRemove", _t('Remove'));
		$this->raintpl->assign( "strFileName", _t('File name ...'));
		$this->raintpl->assign( "size", $config['SIZE']);
        $this->raintpl->assign( "value", $config['VALUE']);
		$this->raintpl->assign( "type", $config['TYPE']);
		//return control
		
		return $this->raintpl->draw('ctr_uploader',true);
	
	}
	
}
?>
