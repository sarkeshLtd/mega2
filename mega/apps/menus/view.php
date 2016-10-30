<?php
namespace Mega\Apps\menus;
use \Mega\Control as control;
use \Mega\Cls\core as core;
use \Mega\Cls\template as template;

class view {
	
	function __construct(){}
	
	/*
	 * show list of menus
	 * @param array of menus $menus
	 * @return array [title,content]
	 */
	public function viewListMenus($menus){
		$form = new control\form("menus_menus_list");
		$table = new control\table;
		$counter = 0;
		foreach($menus as $key=>$menu){
			$counter ++ ;
			$row = new control\row;
			
			//add id to table for count rows
			$lblID = new control\label($counter);
			$row->add($lblID,1);
			
			//add menu name
			$lblMenuName = new control\label($menu->name);
			$row->add($lblMenuName,2);

			//add menu name
			$lblMenuLocalize = new control\label($menu->localize);
			$row->add($lblMenuLocalize,2);
					

			$btnAddLink = new control\button;
			$btnAddLink->configure('LABEL',_t('Add link'));
			$btnAddLink->configure('TYPE','success');
			$btnAddLink->configure('HREF',core\general::createUrl(['service','administrator','load','menus','doLink',$menu->id]));
			$row->add($btnAddLink,1);

			$btnManageLinks = new control\button;
			$btnManageLinks->configure('LABEL',_t('Manage links'));
			$btnManageLinks->configure('TYPE','default');
			$btnManageLinks->configure('HREF',core\general::createUrl(['service','administrator','load','menus','listLinks',$menu->id]));
			$row->add($btnManageLinks,1);

			$btnEdite = new control\button;
			$btnEdite->configure('LABEL',_t('Edite'));
			$btnEdite->configure('TYPE','default');
			$btnEdite->configure('HREF',core\general::createUrl(['service','administrator','load','menus','doMenu',$menu->id]));
			$row->add($btnEdite,1);

			$btnDelete = new control\button;
			$btnDelete->configure('LABEL',_t('Delete'));
			$btnDelete->configure('TYPE','danger');
			$btnDelete->configure('HREF',core\general::createUrl(['service','administrator','load','menus','sureDeleteMenu',$menu->id]));
			$row->add($btnDelete,1);

			$table->add_row($row);
			
		}
		
		//add headers to table
		$table->configure('HEADERS',array(_t('ID'),_t('Name'),_t('Localize'),_t('Add link'),_t('Manage'),_t('Edite'),_t('Delete')));
		$table->configure('HEADERS_WIDTH',[1,5,2,1,1,1,1]);
		$table->configure('ALIGN_CENTER',[TRUE,FALSE,TRUE,TRUE,TRUE,TRUE,TRUE]);
		$table->configure('BORDER',true);
		$form->add($table);

		$btnNewMenu = new control\button;
		$btnNewMenu->configure('LABEL',_t('New menu'));
		$btnNewMenu->configure('TYPE','success');
		$btnNewMenu->configure('HREF',core\general::createUrl(['service','administrator','load','menus','doMenu']));
		$form->add($btnNewMenu,1);
		return [_t('List of menus'),$form->draw()];
	}
	
	/*
	 * insert or edite menu
	 * @param array $localize, all localizes info
	 * @param array $menu, menu info
	 * @return array [title,content]
	 */
	public function viewDoMenu($localizes,$menu = null){
		$form = new control\form('frm_new_menu');

		$txtMenuName = new control\textbox('txtName');
		$txtMenuName->configure('LABEL',_t('Menu name'));
		$txtMenuName->configure('ADDON','*');
		$txtMenuName->configure('SIZE',3);
		
		$txtMenuLabel = new control\textbox('txtHeader');
		$txtMenuLabel->configure('LABEL',_t('Header label'));
		$txtMenuLabel->configure('HELP',_t('This text show above of menu in template.'));
		$txtMenuLabel->configure('ADDON','*');
		$txtMenuLabel->configure('SIZE',3);

		$ckbShowHeader = new control\checkbox('ckbShowHeader');
		$ckbShowHeader->configure('LABEL',_t('Show header label'));
		$ckbShowHeader->configure('HELP',_t('This option use for show or hide label of menu.'));
		

		//$form = new control\form('languages');
		$cobLang = new control\combobox('cobLang');
		$cobLang->configure('LABEL',_t('Localize'));
		$cobLang->configure('HELP',_t('Created menu just will showed in selected localize.'));
		$cobLang->configure('TABLE',$localizes);
		$cobLang->configure('SIZE',4);
		$cobLang->configure('COLUMN_LABELS','language_name');
		$cobLang->configure('COLUMN_VALUES','language');

		$ckbHorizontal = new control\checkbox('ckbHorizontal');
		$ckbHorizontal->configure('LABEL',_t('Horizontal menu'));
		$ckbHorizontal->configure('HELP',_t('if this option checked,menu show in horizontal mode.'));

		//add insert and cancel buttons
		$btnDO = new control\button('btnDO');
		$btnDO->configure('LABEL',_t('Add'));
		$btnDO->configure('P_ONCLICK_PLUGIN','menus');
		$btnDO->configure('P_ONCLICK_FUNCTION','onclickBtnDoMenu');
		$btnDO->configure('TYPE','primary');
		
		$btnCancel = new control\button('btnCancel');
		$btnCancel->configure('LABEL',_t('Cancel'));
		$btnCancel->configure('HREF',core\general::createUrl(['service','administrator','load','menus','listMenus']));
		$header = _t('New menu');
		if(!is_null($menu)){
			$header = sprintf(_t('Edite: %s'),$menu->name);
			$txtMenuName->configure('VALUE',$menu->name);
			$txtMenuLabel->configure('VALUE',$menu->header);
			if($menu->horiz == '1')
				$ckbHorizontal->configure('CHECKED',TRUE);
			if($menu->show_header == '1')
				$ckbShowHeader->configure('CHECKED',TRUE);
			$cobLang->configure('SELECTED_INDEX',$menu->localize);

			//add id of menu to form
			$hidID = new control\hidden('hidID');
			$hidID->configure('VALUE',$menu->id);
			$form->add($hidID);

			//change label of button
			$btnDO->configure('LABEL',_t('Edite'));
		}
		$form->add($txtMenuName);
		$form->add($txtMenuLabel);
		$form->add($ckbShowHeader);
		$form->add($cobLang);
		$form->add($ckbHorizontal);
		
		$row = new control\row;
		$row->configure('IN_TABLE',false);
		
		$row->add($btnDO,1);
		$row->add($btnCancel,11);
		$form->add($row); 
		return [$header,$form->draw()];
	}
	
	/*
	 * Show message for delete menu
	 * @param object $menu, menu information that fetch from database
	 * @return array [title,content]
	 */
	public function viewSureDeleteMenu($menu){
		$form = new control\form('menus_sure_delete_menu');

		$hidID = new control\hidden('hidID');
		$hidID->configure('VALUE',$menu->id);
		$form->add($hidID);

		$lbl = new control\label;
		$lbl->configure('LABEL',_t('Are you sure for delete menu?'));
		$lbl_menu_name = new control\label;
		$lbl_menu_name->configure('LABEL',sprintf(_t('Menu name: %s'),$menu->name));
		$form->add($lbl);
		$form->add($lbl_menu_name);

		//add update and cancel buttons
		$btnDelete = new control\button('btnDelete');
		$btnDelete->configure('LABEL',_t('Delete'));
		$btnDelete->configure('P_ONCLICK_PLUGIN','menus');
		$btnDelete->configure('P_ONCLICK_FUNCTION','onclickBtnDeleteMenu');
		$btnDelete->configure('TYPE','danger');
		
		$btnCancel = new control\button('btnCancel');
		$btnCancel->configure('LABEL',_t('Cancel'));
		$btnCancel->configure('HREF',core\general::createUrl(['service','administrator','load','menus','listMenus']));
		
		$row = new control\row;
		$row->configure('IN_TABLE',false);
		
		$row->add($btnDelete,1);
		$row->add($btnCancel,11);
		$form->add($row);
		return [_t('Delete menu'),$form->draw()];
	}
	
	/*
	 * show list of links in menu
	 * @param array $links, all links that exists in menu
	 * @param integer $menuID, id of menu
	 * @return array [title,content]
	 */
	public function viewListLinks($links,$menuID){
		$form = new control\form("menus_list_links");
		$table = new control\table;
		$counter = 0;
		foreach($links as $key=>$link){
			$counter ++ ;
			$row = new control\row;
			
			//add id to table for count rows
			$lblID = new control\label($counter);
			$row->add($lblID,1);
			
			//add menu name
			$lblLinksName = new control\label($link->label);
			$row->add($lblLinksName,2);
		
			$btnEdite = new control\button;
			$btnEdite->configure('LABEL',_t('Edite'));
			$btnEdite->configure('TYPE','default');
			$btnEdite->configure('HREF',core\general::createUrl(['service','administrator','load','menus','doLink',$link->ref_id,$link->id]));
			$row->add($btnEdite,1);

			$btnDelete = new control\button;
			$btnDelete->configure('LABEL',_t('Delete'));
			$btnDelete->configure('TYPE','danger');
			$btnDelete->configure('HREF',core\general::createUrl(['service','administrator','load','menus','sureDeleteLink',$link->id]));
			$row->add($btnDelete,1);

			$table->add_row($row);
			
		}
		
		//add headers to table
		$table->configure('HEADERS',array(_t('ID'),_t('Name'),_t('Edite'),_t('Delete')));
		$table->configure('HEADERS_WIDTH',[1,5,1,1,]);
		$table->configure('ALIGN_CENTER',[TRUE,FALSE,TRUE,TRUE]);
		$table->configure('BORDER',true);
		$form->add($table);

		//add insert_link and cancel buttons
		$btnNewLink = new control\button('btnNewLink');
		$btnNewLink->configure('LABEL',_t('New link'));
		$btnNewLink->configure('HREF',core\general::createUrl(['service','administrator','load','menus','doLink',$menuID]));
		$btnNewLink->configure('TYPE','success');
		
		$btnCancel = new control\button('btnCancel');
		$btnCancel->configure('LABEL',_t('Cancel'));
		$btnCancel->configure('HREF',core\general::createUrl(['service','administrator','load','menus','listMenus']));
		
		$row = new control\row;
		$row->configure('IN_TABLE',false);
		
		$row->add($btnNewLink,1);
		$row->add($btnCancel,11);
		$form->add($row);
		return [_t('List of links'),$form->draw()];
	}
	
	/*
	 * Show message for delete link
	 * @param object $link, link information that fetch from database
	 * @return array [title,content]
	 */
	public function viewSureDeleteLink($link){
		$form = new control\form('menus_sure_delete_menu');

		$hidID = new control\hidden('hidID');
		$hidID->configure('VALUE',$link->id);
		$form->add($hidID);

        $hidMenuID = new control\hidden('hidMenuID');
        $hidMenuID->configure('VALUE',$link->ref_id);
        $form->add($hidMenuID);

		$lbl = new control\label;
		$lbl->configure('LABEL',_t('Are you sure for delete this link?'));
		$lbl_menu_name = new control\label;
		$lbl_menu_name->configure('LABEL',sprintf(_t('link name: %s'),$link->label));
		$form->add($lbl);
		$form->add($lbl_menu_name);

		//add update and cancel buttons
		$btnDelete = new control\button('btnDelete');
		$btnDelete->configure('LABEL',_t('Delete'));
		$btnDelete->configure('P_ONCLICK_PLUGIN','menus');
		$btnDelete->configure('P_ONCLICK_FUNCTION','onclickBtnDeleteLink');
		$btnDelete->configure('TYPE','danger');
		
		$btnCancel = new control\button('btnCancel');
		$btnCancel->configure('LABEL',_t('Cancel'));
		$btnCancel->configure('HREF',core\general::createUrl(['service','administrator','load','menus','listLinks',$link->ref_id]));
		
		$row = new control\row;
		$row->configure('IN_TABLE',false);
		
		$row->add($btnDelete,1);
		$row->add($btnCancel,11);
		$form->add($row);
		return [_t('Delete link'),$form->draw()];
	}
	
	/*
	 * draw menu in theme
	 * @param array $links, links informations
	 * @return array [title,content]
	 */
	public function viewDrawMenu($links){
		//create an object from raintpl class//
		$raintpl = new template\raintpl;
		//configure raintpl //
		$raintpl->configure('tpl_dir','mega/apps/menus/tpl/');

		$raintpl->assign( "horiz", $links[0]->horiz);
		$raintpl->assign( "show_header", $links[0]->show_header);
		$raintpl->assign( "header", $links[0]->header);
		$raintpl->assign( "links", $links);
		//draw and return back content
		return ['',$raintpl->draw('menu', true )];
	}
	
	/*
	 * insert or update link
	 * @param object $link, link information
	 * @param integer $menuID, id of menu for update or insert new link
	 * @return array [title,content]
	 */
	public function viewDoLink($link=null,$menuID){
		$form = new control\form('menus_add_link');

		$hidMenuID = new control\hidden('hidMenuID');
		$hidMenuID->configure('VALUE',$menuID);

		$txtLinkLabel = new control\textbox('txtLabel');
		$txtLinkLabel->configure('LABEL',_t('Link label'));
		$txtLinkLabel->configure('HELP',_t('This option set label for link.'));
		$txtLinkLabel->configure('ADDON','*');
		$txtLinkLabel->configure('SIZE',3);
		
		$txtLinkUrl = new control\textbox('txtUrl');
		$txtLinkUrl->configure('LABEL',_t('URL'));
		$txtLinkUrl->configure('HELP',_t('This option set address of link.'));
		$txtLinkUrl->configure('ADDON','*');
		$txtLinkUrl->configure('SIZE',4);

		$ckbEnable = new control\checkbox('ckbEnable');
		$ckbEnable->configure('LABEL',_t('Enable link'));
		$ckbEnable->configure('HELP',_t('With this, you can show or hide link in menu.'));

		//create combobox for ranking
		$cobRank = new control\combobox('cobRank');
        $cobRank->configure('LABEL',_t('Rank'));
        $cobRank->configure('SOURCE',[0,1,2,3,4,5,6,7,8,10,11,12,13,14,15,16,17,18,19,20]);
        $cobRank->configure('HELP',_t('use for set position of link in menu.'));
        $cobRank->configure('SIZE',3);

		//add update and cancel buttons
		$btnDo = new control\button('btnDo');
		$btnDo->configure('LABEL',_t('Add'));
		$btnDo->configure('P_ONCLICK_PLUGIN','menus');
		$btnDo->configure('P_ONCLICK_FUNCTION','onclickBtnDoLink');
		$btnDo->configure('TYPE','primary');
		
		$btnCancel = new control\button('btnCancel');
		$btnCancel->configure('LABEL',_t('Cancel'));
		$btnCancel->configure('HREF',core\general::createUrl(['service','administrator','load','menus','listLinks',$menuID]));
		
		$row = new control\row;
		$row->configure('IN_TABLE',false);
		
		$row->add($btnDo,1);
		$row->add($btnCancel,11);

		if(!is_null($link)){
			//edite mode
			if($link->enable == 1) $ckbEnable->checked = true;
			$txtLinkLabel->configure('VALUE',$link->label);
			$txtLinkUrl->configure('VALUE',$link->url);
			$hidID = new control\hidden('hidID');
			$hidID->configure('VALUE',$link->id);
			$btnDo->configure('LABEL',_t('Save'));
			$cobRank->configure('SELECTED_INDEX',$link->rank);
			$form->add($hidID);

		}
		$form->addArray([$txtLinkLabel,$txtLinkUrl,$hidMenuID,$cobRank,$ckbEnable]);
		$form->add($row);   
		return [('Add new link'),$form->draw()];
	}
}
