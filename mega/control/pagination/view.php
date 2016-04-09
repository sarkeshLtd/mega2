<?php
namespace Mega\Control\pagination;
use \Mega\Cls\template as template;
use \Mega\Cls\browser as browser;
class view{
	private $raintpl;
	
	function __construct(){
		$this->raintpl = new template\raintpl;
		$this->raintpl->configure("tpl_dir",'./mega/control/pagination/');
	}
	public function view_draw($config){
		$this->raintpl->assign("strOlder",_('Older'));
        $this->raintpl->assign("strNext",_('Newer'));
        $this->raintpl->assign("nextUrl",$config['NEXT_URL']);
        $this->raintpl->assign("preUrl",$config['PRE_URL']);
		return $this->raintpl->draw("pagination_simple",true);
	}
}
?>
