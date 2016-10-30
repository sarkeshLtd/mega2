<?php
namespace Mega\Apps\page;
use \Mega\Control as control;
use \Mega\Cls\browser as browser;
use \Mega\Cls\template as template;
use \Mega\Cls\core as core;

trait view {
	use \Mega\Apps\Files\addons;

    /*
    * show message for login or signup message
    * return string,html content
    */
    protected function viewLoginReq(){
        $btnLogin = new control\button('btnLogin');
        $btnLogin->label = _t('Login');
        $btnLogin->type = 'link';
        $btnLogin->href = core\general::createUrl(['users','login']);

        $btnRegister = new control\button('$btnRegister');
        $btnRegister->label = _t('Register');
        $btnRegister->type = 'link';
        $btnRegister->href = core\general::createUrl(['users','register']);

        $label = new control\label('<div class="well well-sm">' . sprintf(_t('For submit new comment %s or %s'),$btnLogin->draw(),$btnRegister->draw()) . '</div>');
        return $label->draw();
    }

	/*
	 * show page with id
	 * @param object $post, post information
	 * @param array $comments, all comments of post
	 * @param array $settings, plugin settings
	 * @RETURN html content [title,body]
	 */
	protected function viewShow($post,$comments,$settings){
		$raintpl = new template\raintpl;
		//configure raintpl //
		$raintpl->configure('tpl_dir', APP_PATH . '/mega/apps/page/tpl/');
		//Assign variables
		$raintpl->assign( "TITLE", $post->title);

        if(strlen($post->tags) != 0)
            $raintpl->assign( "TAGS", $post->tags);

        if($this->isLogedin())
            $raintpl->assign( "commentForm", $this->viewGetCommentForm($post->id));
        else
            $raintpl->assign( "commentForm", $this->viewLoginReq());

        if(!is_null($comments))
            $raintpl->assign( "comments", $this->viewComments($comments));
        $raintpl->assign( "strComments",_t('Comments:'));

        $raintpl->assign( "strTags", _t('Tags:'));
		$raintpl->assign( "BODY", $post->body);
        $hasImage = false;
        $fileAdr = '';
        $raintpl->assign( "image", '');
        if($post->photo != ''){
            $hasImage = true;
            $fileAdr = $this->getFileAddress($post->photo);
            $raintpl->assign( "image", $hasImage);
            $raintpl->assign( "fileAdr", $fileAdr);

        }
		$calendar = \Mega\Cls\calendar\calendar::singleton();
        $infoString = '';
        if($settings->showAuthor == 1)
            $infoString = sprintf(_t('Post by %s'),$post->username);
        if($settings->showDate == 1)
            $infoString .=  ' ' .sprintf(_t('in %s'),$calendar->cdate($settings->postDateFormat,$post->date));
		$raintpl->assign( "INFO", $infoString);
		return [$post->title,$raintpl->draw('post',true)];
	}
	
	/*
	 * show list of catalogues
	 * @param array $cats , all catalogues information
	 * @RETURN html content [title,body]
	 */
	protected function viewCatalogues($cats){
		$form = new control\form('blog_cat_table');
		$table = new control\table('blog_cat_table');
		$counter = 0;
        if(!is_null($cats)) {
            foreach ($cats as $key => $cat) {
                $counter += 1;
                $row = new control\row('blog_cat_row');

                $lbl_id = new control\label('lbl');
                $lbl_id->configure('LABEL', $counter);
                $row->add($lbl_id, 1);

                $lbl_cat = new control\label('lbl');
                $lbl_cat->configure('LABEL', $cat->name);
                $row->add($lbl_cat, 1);

                $lbl_cat = new control\label('lbl');
                $lbl_cat->configure('LABEL', $cat->language_name);
                $row->add($lbl_cat, 1);

                $btn_edite = new control\button('btn_content_cats_edite');
                $btn_edite->configure('LABEL', _t('Edit'));
                $btn_edite->configure('VALUE', $cat->id);
                $btn_edite->configure('HREF', core\general::createUrl(['service', 'administrator', 'load', 'page', 'editeCat', $cat->id]));
                $row->add($btn_edite, 2);

                $btn_delete = new control\button('btn_content_cats_delete');
                $btn_delete->configure('LABEL', _t('Delete'));
                $btn_delete->configure('HREF', core\general::createUrl(['service', 'administrator', 'load', 'page', 'sureDeleteCat', $cat->id]));
                $btn_delete->configure('TYPE', 'danger');
                $row->add($btn_delete, 2);

                $table->add_row($row);
                $table->configure('HEADERS', [_t('ID'), _t('Name'), _t('Localize'), _t('Edit'), _t('Delete')]);
                $table->configure('HEADERS_WIDTH', [1, 7, 2, 1, 1]);
                $table->configure('ALIGN_CENTER', [TRUE, FALSE, TRUE, TRUE, TRUE]);
                $table->configure('BORDER', true);
                $table->configure('SIZE', 9);
            }
            $form->add($table);
        }
        else{
            //catalogues not found
            $abelNotFound = new control\label(_t('No catalogue added.first add a catalogue.'));
            $form->add($abelNotFound);
        }

		$btn_add_cats = new control\button('btn_add_cats');
		$btn_add_cats->configure('LABEL',_t('Add new catalogue'));
		$btn_add_cats->configure('TYPE','success');
		$btn_add_cats->configure('HREF',core\general::createUrl(['service','administrator','load','page','newCat']));
		$form->add($btn_add_cats);
		
		return [_t('Catalogues'),$form->draw()];
	}


    /*
	 * show form for add new catalogue
     * @param object $local, localize information
     * @param object $settings, plugin settings
	 * @RETURN html content [title,body]
	 */
    protected function viewNewCat($languages,$local){
        $form = new control\form('frmNewCatalogue');

        $txtName = new control\textbox('txtName');
        $txtName->label = _t('Catalogue name');
        $txtName->place_holder = _t('Catalogue name');
        $txtName->size = 4;
        $form->add($txtName);

        $btnAddCat = new control\button('btnAddCat');
        $btnAddCat->configure('LABEL',_t('Add catalogue'));
        $btnAddCat->configure('TYPE','primary');
        $btnAddCat->p_onclick_plugin = 'page';
        $btnAddCat->p_onclick_function = 'btnOnclickAddCat';

        $ckbCanComment = new control\checkbox('ckbCanComment');
        $ckbCanComment->label = _t('Allow users and guests for submit comment?');
        $form->add($ckbCanComment);

        $cobLang = new control\combobox('cobLang');
        $cobLang->configure('LABEL',_t('Localize'));
        $cobLang->configure('HELP',_t('select language of this catalogue'));
        $cobLang->configure('TABLE',$languages);
        $cobLang->configure('COLUMN_VALUES','id');
        $cobLang->configure('COLUMN_LABELS','language_name');
        $cobLang->configure('SELECTED_INDEX',$local->id);
        $cobLang->configure('SIZE',3);
        $form->add($cobLang);

        $btn_cancel = new control\button('btn_cancel');
        $btn_cancel->configure('LABEL',_t('Cancel'));
        $btn_cancel->configure('HREF',core\general::createUrl(['service','administrator','load','page','catalogues']));

        $row = new control\row;
        $row->configure('IN_TABLE',false);

        $row->add($btnAddCat,2);
        $row->add($btn_cancel,10);
        $form->add($row);

        return [_t('New catalogue'),$form->draw()];
    }
    
    /*
	 * show form for delete catalogue
	 * @param object $cat, catalogue information
	 * @RETURN html content [title,body]
	 */
    protected function viewSureDeletCat($cat){
       $form = new control\form('frmSureDeletCat');
       
       $hidID = new control\hidden('hidID');
       $hidID->value = $cat->id;
       $form->add($hidID);
       
       $label = new control\label(sprintf(_t('Are you sure for delete %s'),$cat->name));
       $form->add($label);
       
       $btnDelete = new control\button('btnDelete');
       $btnDelete->configure('LABEL',_t('Yes, Delete'));
       $btnDelete->configure('TYPE','primary');
       $btnDelete->p_onclick_plugin = 'page';
       $btnDelete->p_onclick_function = 'btnOnclickDeleteCat';
        
       $btn_cancel = new control\button('btn_cancel');
       $btn_cancel->configure('LABEL',_t('Cancel'));
       $btn_cancel->configure('HREF',core\general::createUrl(['service','administrator','load','page','catalogues']));

       $row = new control\row;
       $row->configure('IN_TABLE',false);

       $row->add($btnDelete,1);
       $row->add($btn_cancel,11);
       $form->add($row);
       
       return [sprintf(_t('Delete %s'),$cat->name),$form->draw()];
    }
    
    /*
	 * edite catalogue form
	 * @param object $cat, catalogue information
	 * @param object $local, localize information
     * @param object $settings, plugin settings
	 * @RETURN html content [title,body]
	 */
    protected function viewEditeCat($cat,$languages,$local){
        $form = new control\form('frmNewCatalogue');
		
		$hidID = new control\hidden('hidID');
		$hidID->value = $cat->id;
		$form->add($hidID);
		
        $txtName = new control\textbox('txtName');
        $txtName->label = _t('Catalogue name');
        $txtName->value = $cat->name;
        $txtName->place_holder = _t('Catalogue name');
        $txtName->size = 4;
        $form->add($txtName);


        $btnAddCat = new control\button('btnEditeCat');
        $btnAddCat->configure('LABEL',_t('Save changes'));
        $btnAddCat->configure('TYPE','primary');
        $btnAddCat->p_onclick_plugin = 'page';
        $btnAddCat->p_onclick_function = 'btnOnclickEditeCat';

        $ckbCanComment = new control\checkbox('ckbCanComment');
        $ckbCanComment->label = _t('Allow users and guests for submit comment?');
        $ckbCanComment->checked = false;
        if($cat->canComment == 1)
			$ckbCanComment->checked = true;
        $form->add($ckbCanComment);

        $cobLang = new control\combobox('cobLang');
        $cobLang->configure('LABEL',_t('Default users roll'));
        $cobLang->configure('HELP',_t('New users get roll that you select in above.'));
        $cobLang->configure('TABLE',$languages);
        $cobLang->configure('COLUMN_VALUES','id');
        $cobLang->configure('COLUMN_LABELS','language_name');
        $cobLang->configure('SELECTED_INDEX',$cat->localize);
        $cobLang->configure('SIZE',3);
        $form->add($cobLang);

        $btn_cancel = new control\button('btn_cancel');
        $btn_cancel->configure('LABEL',_t('Cancel'));
        $btn_cancel->configure('HREF',core\general::createUrl(['service','administrator','load','page','catalogues']));

        $row = new control\row;
        $row->configure('IN_TABLE',false);

        $row->add($btnAddCat,2);
        $row->add($btn_cancel,10);
        $form->add($row);

        return [sprintf(_t('Edite %s'),$cat->name),$form->draw()];
	}
	
	 /*
     * show settings page
     * @param object $settings, plugin settings that stored in registry
     * @RETURN html content [title,body]
     */
    protected function viewSettings($settings){
		$form = new control\form('blog_settings');
		
        //show author of post
        $ckbShowAuthor = new control\checkbox('ckbShowAuthor');
		$ckbShowAuthor->configure('LABEL',_t('Show author') );
		
		$ckbShowAuthor->configure('HELP',_t('If checked,author of post show in posts and catlogues.'));
		if($settings->showAuthor == 1)
			$ckbShowAuthor->configure('CHECKED',TRUE);
		$form->add($ckbShowAuthor);
		
		
		//show date of post
        $ckbShowDate = new control\checkbox('ckbShowDate');
		$ckbShowDate->configure('LABEL',_t('Show date') );
		$ckbShowDate->configure('HELP',_t('If checked,date will showed in post content.'));
		if($settings->showDate == 1)
			$ckbShowDate->configure('CHECKED',TRUE);
		$form->add($ckbShowDate);
		//set number of post per page
		$cobPerPage = new control\combobox('cobPerPage');
        $cobPerPage->configure('LABEL',_t('Posts per page'));
        $cobPerPage->configure('HELP',_t('This option set number of post that can show per page.'));
        $cobPerPage->configure('SOURCE',[1,2,3,4,5,6,7,8,10,11,12,13,14,15,16,17,18,19,20]);
        $cobPerPage->configure('SELECTED_INDEX',$settings->PostPerPage);
        $cobPerPage->configure('SIZE',4);
        $form->add($cobPerPage);
		
        //add update and cancel buttons
		$btnUpdate = new control\button('btnUpdate');
		$btnUpdate->configure('LABEL',_t('Update'));
		$btnUpdate->configure('P_ONCLICK_PLUGIN','page');
		$btnUpdate->configure('P_ONCLICK_FUNCTION','btnOnclickSaveSettings');
		$btnUpdate->configure('TYPE','primary');
		
		$btnCancel = new control\button('btnCancel');
		$btnCancel->configure('LABEL',_t('Cancel'));
		$btnCancel->configure('HREF',core\general::createUrl(['service','administrator','load','administrator','dashboard']));
		
		$row = new control\row;
		$row->configure('IN_TABLE',false);
		
		$row->add($btnUpdate,1);
		$row->add($btnCancel,11);
		$form->add($row);  

		return [_t('page settings'),$form->draw()];
	}

    /*
     * for submit new post
     * @param array $settings, plugin settings
     * @return array html content [ title,content]
     */
    protected function viewDoPage($settings,$cats,$page = null){
        $form = new control\form('frmNewPost');

        $txtTitle = new control\textbox('txtTitle');
        $txtTitle->label = _t('Title:');
        $txtTitle->PLACE_HOLDER = _t('Your title in here!');

        $txtBody = new control\textarea('txtBody');
        $txtBody->label = _t('Body:');
        $txtBody->editor = true;

        $txtTags = new control\textbox('txtTags');
        $txtTags->label = _t('Tags:');
        $txtTags->help = _t("seperate tags with ','.");

        $uplPhoto = new control\uploader('uplPhoto');
        $uplPhoto->label = _t('Featured image');
        $uplPhoto->max_file_size = 65536 * 1024;
        $uplPhoto->help = _t('Set featured image');

        $cobCatalogue = new control\combobox('cobCatalogue');
        $cobCatalogue->configure('LABEL',_t('Catalogue'));
        $cobCatalogue->configure('TABLE',$cats);
        $cobCatalogue->configure('COLUMN_VALUES','id');
        $cobCatalogue->configure('COLUMN_LABELS','name');
        $cobCatalogue->configure('SIZE',3);
        
        $ckbPublish = new control\checkbox('ckbPublish');
        $ckbPublish->label = _t('Publish page?');
        $ckbPublish->help = _t('Uncheck for save page without publish for other.');
        $ckbPublish->checked = true;

		if(! is_null($page)){
			$hidID = new control\hidden('hidID');
			$hidID->value = $page->id;
			$form->add($hidID);
			$txtTitle->value = $page->title;
			$txtBody->value = $page->body;
			$txtTags->value = $page->tags;
			$uplPhoto->value = $page->photo;
			$cobCatalogue->selected_index = $page->catalogue;
			$ckbPublish->checked = true;
			if($page->publish == 1)
				$ckbPublish->checked = true;
		}
		$form->addArray([$txtTitle,$txtBody,$txtTags,$uplPhoto,$cobCatalogue,$ckbPublish]);
        //add update and cancel buttons
        $btnSubmit = new control\button('btnSubmit');
        $btnSubmit->configure('LABEL',_t('Submit'));
        $btnSubmit->configure('P_ONCLICK_PLUGIN','page');
        $btnSubmit->configure('P_ONCLICK_FUNCTION','btnOnclickSubmitPage');
        $btnSubmit->configure('TYPE','primary');

        $btnCancel = new control\button('btnCancel');
        $btnCancel->configure('LABEL',_t('Cancel'));
        $btnCancel->configure('HREF',core\general::createUrl(['service','administrator','load','page','listPages']));

        $row = new control\row;
        $row->configure('IN_TABLE',false);

        $row->add($btnSubmit,1);
        $row->add($btnCancel,11);
        $form->add($row);
        return [_t('New Page'), $form->draw()];
    }
    
    /*
     * this function show message to user to add catalogue
     * @return array [title,msg]
     */
    protected function viewMsgAddCatalogue(){
		$form = new control\form('frmMsgAddCat');
		$label = new control\label(_t('Please add catalogue before submit new page!'));
		$form->add($label);
		
		$btnAddCat = new control\button('btnAddCat');
		$btnAddCat->label = _t('Add catalogue');
		$btnAddCat->type = 'default';
		$btnAddCat->href = core\general::createUrl(['service','administrator','load','page','newCat']);
		$form->add($btnAddCat);
		return [_t('Error!'),$form->draw()];
	}
	
	 /*
	 * show list of all pages
	 * @param array $posts, pages for show
	 * $param boolean $hasPre, has priveus page
	 * @param boolean $hasNext, has next page
	 * @param integer $pageNum, page number
	 * @RETURN html content [title,body]
	 */
    protected function viewListPages($posts,$hasPre,$hasNext,$pageNum){
        $form = new control\form('blog_list_posts');
        
        $btn_add_post = new control\button('btn_add_post');
		$btn_add_post->configure('LABEL',_t('New post'));
		$btn_add_post->configure('TYPE','success');
		$btn_add_post->configure('HREF',core\general::createUrl(['service','administrator','load','page','newPage']));
		$form->add($btn_add_post);
		
		$table = new control\table('blog_list_posts');
		$counter = 0;
        if(is_array($posts))
            foreach($posts as $key=>$post){
                $counter += 1;
                $row = new control\row('blog_cat_row');

                $lbl_id = new control\label('lbl');
                $lbl_id->configure('LABEL',$counter);
                $row->add($lbl_id,1);

                $btn_header = new control\button('lbl');
                $btn_header->configure('LABEL',$post->title);
                $btn_header->configure('TYPE','link');
                $btn_header->configure('HREF',core\general::createUrl(['page','show',$post->adr]));
                $row->add($btn_header,1);

                $lbl_loc = new control\label('lbl');
                $lbl_loc->configure('LABEL',$post->name);
                $row->add($lbl_loc,1);

                $btn_edite = new control\button('btn_content_cats_edite');
                $btn_edite->configure('LABEL',_t('Edit'));
                $btn_edite->configure('HREF',core\general::createUrl(['service','administrator','load','page','editePost',$post->id]));
                $row->add($btn_edite,2);

                $btn_delete = new control\button('btn_content_cats_delete');
                $btn_delete->configure('LABEL',_t('Delete'));
                $btn_delete->configure('HREF',core\general::createUrl(['service','administrator','load','page','sureDeletePage',$post->id]));
                $btn_delete->configure('TYPE','danger');
                $row->add($btn_delete,2);

                $table->add_row($row);

            }
        $table->configure('HEADERS',[_t('ID'),_t('Header'),_t('Catalogue'),_t('Edit'),_t('Delete')]);
        $table->configure('HEADERS_WIDTH',[1,7,2,1,1]);
        $table->configure('ALIGN_CENTER',[TRUE,FALSE,TRUE,TRUE,TRUE]);
        $table->configure('BORDER',true);
        $table->configure('SIZE',9);
		$form->add($table);
		
		$row = new control\row;
        $row->configure('IN_TABLE',false);
		if($hasPre){
			//add update and cancel buttons
			$btnPre = new control\button('btnPre');
			$btnPre->configure('LABEL',_t('Privius'));
			$btnPre->configure('HREF',core\general::createUrl(['service','administrator','load','page','listPages',$pageNum - 1]));
			$row->add($btnPre,6);
		}
		if($hasNext){
			$btnNext = new control\button('btnNext');
			$btnNext->configure('LABEL',_t('Next'));
			$btnNext->configure('HREF',core\general::createUrl(['service','administrator','load','page','listPages',$pageNum + 1]));
			$row->add($btnNext,6);
		}
		$form->add($row);
		return [_t('Blog posts'),$form->draw()];
    }
    
    /*
	 * show page for delete page
	 * @param object $post, post information
	 * @RETURN html content [title,body]
	 */
    protected function viewSureDeletPost($post){
        $form = new control\form('frmSureDeletCat');
       
       $hidID = new control\hidden('hidID');
       $hidID->value = $post->id;
       $form->add($hidID);
       
       $label = new control\label(sprintf(_t('Are you sure for delete %s'),$post->title));
       $form->add($label);
       
       $btnDelete = new control\button('btnDelete');
       $btnDelete->configure('LABEL',_t('Yes, Delete'));
       $btnDelete->configure('TYPE','primary');
       $btnDelete->p_onclick_plugin = 'page';
       $btnDelete->p_onclick_function = 'btnOnclickDeletePost';
        
       $btn_cancel = new control\button('btn_cancel');
       $btn_cancel->configure('LABEL',_t('Cancel'));
       $btn_cancel->configure('HREF',core\general::createUrl(['service','administrator','load','page','listPages']));

       $row = new control\row;
       $row->configure('IN_TABLE',false);

       $row->add($btnDelete,1);
       $row->add($btn_cancel,11);
       $form->add($row);
       
       return [sprintf(_t('Delete %s'),$post->name),$form->draw()];
    }
    
    /*
	 * show pages in catalogue
	 * @param array $pages, all pages in catalogue
	 * @param object $cat, catalogue information
     * @param boolean $hasNext, has next page
     * @param boolean $hasPre , has previous page
	 * @RETURN html content [title,body]
	 */
    protected function viewShowCtatlogePages($pages,$cat,$hasNext,$hasPre,$page){
	$raintpl = template\raintpl::singleton();
	//configure raintpl //
	$raintpl->configure('tpl_dir', APP_PATH . '/mega/apps/page/tpl/');
	//Assign variables
	$raintpl->assign( "pages", $pages);
	$raintpl->assign( "baseAdr", core\general::createUrl(['page','show']));
	$tile = new control\tile('tileCatalogue');

        $tile->add($raintpl->draw('catalogue',true));

        $pagination = new control\pagination();
        if($hasNext)
            $pagination->next_Url = core\general::createUrl(['page','catalogue',$cat->id,$page + 1]);
        if($hasPre)
            $pagination->pre_Url = core\general::createUrl(['page','catalogue',$cat->id,$page - 1]);

        $tile->add($pagination->draw());

		return [$cat->name,$tile->draw()];
    }

    /**
     * show submit comment form.
     * @param integer $topicID, id of topic
     * @return string ,html content
     */
    protected function viewGetCommentForm($topicID){
        $form = new control\form('frmNewComment');

        $pageID = new control\hidden('pageID');
        $pageID->value = $topicID;
        $form->add($pageID);

        $txtBody = new control\textarea('txtBody');
        $txtBody->label = _t('Your comment:');
        $txtBody->editor = false;
        $txtBody->rows = 5;
        $form->add($txtBody);

        $btnSubmit = new control\button('btnSubmitComment');
        $btnSubmit->type = 'primary';
        $btnSubmit->p_onclick_plugin = 'page';
        $btnSubmit->p_onclick_function = 'onclickSubmitComment';
        $btnSubmit->label = _t('Submit comment');
        $form->add($btnSubmit);

        return $form->draw();
    }

    /**
     * show submit comment form.
     * @param integer $topicID, id of topic
     * @return string ,html content
     */
    protected function viewComments($comments){
        $tile = new control\tile('tileComments');
        foreach($comments as $comment){
            $imgAvatar = new control\image('imgAvatar');
            $imgAvatar->type = 'img-rounded';
            $imgAvatar->style = "width:64px;";
            $imgAvatar->src = DOMAIN_EXE . '/plugins/defined/users/images/def_avatar_128.png';
            if($this->fileExists($comment->photo))
                $imgAvatar->src = $this->getFileAddress($comment->photo);
            $calendar = \Mega\Cls\calendar\calendar::singleton();
            $header = sprintf(_t('%s in %s'),$comment->username,$calendar->cdate('Y/m/d H:i:s',$comment->date));
            $body = $imgAvatar->draw() . $comment->body;
            $label = new control\label(browser\page::showBlock($header,$body,'BLOCK','primary'));
            $tile->add($label->draw());
        }
        return $tile->draw();
    }

    /**
     * show list of comments
     * @param array $comments, comments infermation
     * @return html content [title,body]
     */
    protected function viewListComments($comments,$hasNext,$hasPre,$pageNum){
        $form = new control\form('frmPageNewComments');
        $table = new control\table('tbl');
        $counter = 1;
        if(is_array($comments))
            foreach($comments as $comment){
                $row = new control\row();
                $lblID = new control\label($counter);
                $row->add($lblID,1);
                $counter ++;

                $lblUser = new control\label($comment->username);
                $row->add($lblUser,2);

                $lblComment = new control\label($comment->body);
                $row->add($lblComment,7);

                $btnDelete = new control\button('btnDelete');
                $btnDelete->value = $comment->id;
                $btnDelete->label = _t('Delete');
                $btnDelete->p_onclick_plugin = 'page';
                $btnDelete->p_onclick_function = 'btnDeleteComment';
                $btnDelete->type = 'danger';
                $row->add($btnDelete,1);

                $table->add_row($row);
            }
        $table->configure('HEADERS',[_t('ID'),_t('User'),_t('Comment'),_t('Delete')]);
        $table->configure('HEADERS_WIDTH',[1,1,7,2]);
        $table->configure('ALIGN_CENTER',[TRUE,TRUE,FALSE,TRUE]);
        $table->configure('BORDER',true);
        $form->add($table);

        $pagination = new control\pagination();
        if($hasNext)
            $pagination->next_url = core\general::createUrl(['service','administrator','load','page','listComments',$pageNum + 1]);
        if($hasPre)
            $pagination->pre_url = core\general::createUrl(['service','administrator','load','page','listComments',$pageNum - 1]);
        $form->add($pagination);
        return [_t('Comments'),$form->draw()];
    }


}
