<?php
namespace Mega\Control\textarea;
use \Mega\Cls\template as template;
use \Mega\Cls\browser as browser;
class view{
	
	private $raintpl;
	
	function __construct(){
		$this->raintpl = template\raintpl::singleton();
		$this->raintpl->configure("tpl_dir","./mega/control/textarea/");
	}
	protected function view_draw($config){
		
		if($config['EDITOR'] && $config['DEFAULT_EDITOR'] == 'nicedit'){
				browser\page::addHeader('<script src="' . DOMAIN_EXE . '/mega/control/textarea/editors/nicedit/nicEdit.js" type="text/javascript"></script>');
}       elseif($config['EDITOR'] && $config['DEFAULT_EDITOR'] == 'summernote'){
            browser\page::addHeader('<script src="' . DOMAIN_EXE . '/mega/control/textarea/editors/summernote/summernote.min.js" type="text/javascript"></script>');
            browser\page::addHeader('<link rel="stylesheet" type="text/css" href="' . DOMAIN_EXE . '/mega/control/textarea/editors/summernote/summernote.css' . '" />');

        }
		if($config['CSS_FILE'] != ''){ browser\page::addHeader('<link rel="stylesheet" type="text/css" href="' . $config['CSS_FILE']) . '" />';}

		
		$this->raintpl->assign("name",$config['NAME']);
		$this->raintpl->assign("label",$config['LABEL']);
		$this->raintpl->assign("help",$config['HELP']);
		$this->raintpl->assign("id",$config['NAME']);
		$this->raintpl->assign("rows",$config['ROWS']);
		$this->raintpl->assign("size",$config['SIZE']);
		$this->raintpl->assign("style",$config['STYLE']);
		$this->raintpl->assign("value",$config['VALUE']);
		$this->raintpl->assign("defEditor",$config['DEFAULT_EDITOR']);
		$this->raintpl->assign("form",$config['FORM']);
		$this->raintpl->assign("class",$config['CLASS']);
		$this->raintpl->configure("tpl_dir","./mega/control/textarea/");
		return $this->raintpl->draw("ctr_textarea",true);
	}
}
?>
