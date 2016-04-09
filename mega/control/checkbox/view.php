<?php
namespace Mega\Control\checkbox;
use \Mega\Cls\template as template;
use \Mega\Cls\browser as browser;
class view{
	private $raintpl;
	private $page;
	function __construct(){
		$this->page = browser\page::singleton();
		$this->raintpl = template\raintpl::singleton();
	}
	
	public function view_draw($config, $show){
		//configure raintpl //
		$this->raintpl->configure('tpl_dir','mega/control/checkbox/');
		
		//add headers to page//
		browser\page::addHeader('<script src="' . DOMAIN_EXE . '/Etc/scripts/events/functions.js"></script>');
		if($config['SCRIPT_SRC'] != ''){browser\page::addHeader('<script src="' . $config['SCRIPT_SRC'] . '"></script>'); }		
		if($config['CSS_FILE'] != ''){ browser\page::addHeader('<link rel="stylesheet" type="text/css" href="' . $config['CSS_FILE']) . '" />';}
		if($config['SWITCH']){
			browser\page::addHeader('<script src="' . DOMAIN_EXE . '/mega/control/checkbox/ect/bootstrap-switch.min.js"></script>');
			browser\page::addHeader('<link rel="stylesheet" type="text/css" href="' . DOMAIN_EXE . '/mega/control/checkbox/ect/bootstrap-switch.min.css" />');
		}
		
		//Assign variables
		$this->raintpl->assign( "id", $config['NAME']);
		$this->raintpl->assign( "form", $config['FORM']);
		$this->raintpl->assign( "index", $config['INDEX']);
		$this->raintpl->assign( "value", $config['VALUE']);
		$this->raintpl->assign( "show_label", $config['SHOW_LABEL']);
		$this->raintpl->assign( "label", $config['LABEL']);
		$this->raintpl->assign( "help", $config['HELP']);
		$this->raintpl->assign( "size", $config['SIZE']);
		$this->raintpl->assign( "checked", $config['CHECKED']);
		$this->raintpl->assign( "styles", $config['STYLE']);
		$this->raintpl->assign( "class", $config['CLASS']);
		$this->raintpl->assign( "switch", $config['SWITCH']);
		$this->raintpl->assign( "ontext", $config['SWITCH_ONTEXT']);
		$this->raintpl->assign( "offtext", $config['SWITCH_OFFTEXT']);
		$this->raintpl->assign( "j_onclick", $config['J_ONCLICK']);
		$this->raintpl->assign( "p_onclick_f", $config['P_ONCLICK_FUNCTION']);
		$this->raintpl->assign( "p_onclick_p", $config['P_ONCLICK_PLUGIN']);
		$this->raintpl->assign( "j_after_onclick", $config['J_AFTER_ONCLICK']);

		$this->raintpl->assign( "j_onfocus", $config['J_ONFOCUS']);
		$this->raintpl->assign( "p_onfocus_f", $config['P_ONFOCUS_FUNCTION']);
		$this->raintpl->assign( "p_onfocus_p", $config['P_ONFOCUS_PLUGIN']);
		$this->raintpl->assign( "j_after_onfocus", $config['J_AFTER_ONFOCUS']);
		
		$this->raintpl->assign( "j_onblur", $config['J_ONBLUR']);
		$this->raintpl->assign( "p_onblur_f", $config['P_ONBLUR_FUNCTION']);
		$this->raintpl->assign( "p_onblur_p", $config['P_ONBLUR_PLUGIN']);
		$this->raintpl->assign( "j_after_onblur", $config['J_AFTER_ONBLUR']);
		
		
		if($config['DISABLE']){
			$this->raintpl->assign( "disabled", 'disabled');
		}
		else{
			$this->raintpl->assign( "disabled", 'enabled');
		}
		
		
		//return control
		$ctr = $this->raintpl->draw('ctr_checkbox', true );
		if($show){
			echo $ctr;
		}	
		return $ctr;
			
	}
}
?>
