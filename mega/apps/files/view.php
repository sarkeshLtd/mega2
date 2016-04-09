<?php
namespace Mega\Apps\files;
use \Mega\Control as control;
use \Mega\Cls\core as core;

trait view {
	
	/*
	 * show php error log
	 * @param array file errors
	 * @return 2D array [title,content]
	 */
	public function viewPhpErrors($file){
		$content='';
		foreach($file as $line){
			$content =$content . $line . '</br>';
		}
		$label = new control\label('lbl_logs');
		$label->configure('LABEL',$content);
		
		$form = new control\form('reportsPhpErrors');
		$form->add($label);
		//add update and cancel buttons
		$btnClear = new control\button('btnClear');
		$btnClear->configure('LABEL',_('Clear Errors'));
		$btnClear->configure('P_ONCLICK_PLUGIN','reports');
		$btnClear->configure('P_ONCLICK_FUNCTION','onclickBtnClearPhpErrors');
		$btnClear->configure('TYPE','primary');
			
		$btnCancel = new control\button('btnCancel');
		$btnCancel->configure('LABEL',_('Cancel'));
		$btnCancel->configure('HREF',core\general::createUrl(['service','administrator','load','administrator','dashboard']));
			
		$row = new control\row;
		$row->configure('IN_TABLE',false);
			
		$row->add($btnClear,2);
		$row->add($btnCancel,10);
		$form->add($row);                
		return [_('PHP Error logs'),$form->draw()];
	}
}
