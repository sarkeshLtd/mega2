<?php
namespace apps\forum;
use Mega\control as control;
use Mega\cls\core as core;
use Mega\cls\Database as db;
use Mega\cls\browser as browser;

class view {
    use addons;
	use \Mega\Apps\Files\addons;
    use \Mega\Apps\Menus\addons;
	/*
	 * show list of forums
	 * @param array $forums , all forums information
	 * @RETURN html content [title,body]
	 */
	protected function viewListForums($forums){
		$form = new control\form('forumsListForums');
		$table = new control\table('tblListForums');
		$counter = 0;
        if(!is_null($forums)) {
            foreach ($forums as $key => $forum) {
                $counter += 1;
                $row = new control\row('blog_cat_row');

                $lbl_id = new control\label('lbl');
                $lbl_id->configure('LABEL', $counter);
                $row->add($lbl_id, 1);
				
				$tile = new control\tile('tilLitsFORUMS');
                $lblForumName = new control\label('lbl');
                $lblForumDes = new control\label($forum->des);
                $lblForumDes->type = 'success';
                $lblForumName->configure('LABEL', $forum->name);
                $tile->add($lblForumName->draw());
                $tile->add($lblForumDes->draw());
                $row->add($tile, 1);

                $lbl_forum = new control\label('lbl');
                $lbl_forum->configure('LABEL', $forum->language_name);
                $row->add($lbl_forum, 1);

                $btn_edite = new control\button('btn_content_forums_edite');
                $btn_edite->configure('LABEL', _('Edit'));
                $btn_edite->configure('VALUE', $forum->id);
                $btn_edite->configure('HREF', core\general::createUrl(['service', 'administrator', 'load', 'forum', 'editeForum', $forum->id]));
                $row->add($btn_edite, 2);

                $btn_delete = new control\button('btn_content_forums_delete');
                $btn_delete->configure('LABEL', _('Delete'));
                $btn_delete->configure('HREF', core\general::createUrl(['service', 'administrator', 'load', 'forum', 'sureDeleteforum', $forum->id]));
                $btn_delete->configure('TYPE', 'danger');
                $row->add($btn_delete, 2);

                $table->add_row($row);
                $table->configure('HEADERS', [_('ID'), _('Name and description'), _('Localize'), _('Edit'), _('Delete')]);
                $table->configure('HEADERS_WIDTH', [1, 7, 2, 1, 1]);
                $table->configure('ALIGN_CENTER', [TRUE, FALSE, TRUE, TRUE, TRUE]);
                $table->configure('BORDER', true);
                $table->configure('SIZE', 9);
            }
            $form->add($table);
        }
        else{
            //forumalogues not found
            $abelNotFound = new control\label(_('No forum added.first add a forum.'));
            $form->add($abelNotFound);
        }

		$btn_add_forums = new control\button('btn_add_forums');
		$btn_add_forums->configure('LABEL',_('Add new forum'));
		$btn_add_forums->configure('TYPE','success');
		$btn_add_forums->configure('HREF',core\general::createUrl(['service','administrator','load','forum','newforum']));
		$form->add($btn_add_forums);
		
		return [_('Forums'),$form->draw()];
	}
	
	 /*
	 * show form for add new catalogue
     * @param object $local, localize information
     * @param object $settings, plugin settings
	 * @RETURN html content [title,body]
	 */
    protected function viewNewDoForum($languages,$local,$forum=null){
        $form = new control\form('frmNewforum');

        $txtName = new control\textbox('txtName');
        $txtName->label = _('forum name');
        $txtName->place_holder = _('forum name');
        $txtName->size = 4;
        
        $txtDes = new control\textarea('txtDes');
		$txtDes->editor = false;
		$txtDes->label = _('Description');
		
        $cobLang = new control\combobox('cobLang');
        $cobLang->configure('LABEL',_('Default users roll'));
        $cobLang->configure('HELP',_('forum just show in selected localize'));
        $cobLang->configure('TABLE',$languages);
        $cobLang->configure('COLUMN_VALUES','id');
        $cobLang->configure('COLUMN_LABELS','language_name');
        $cobLang->configure('SELECTED_INDEX',$local->id);
        $cobLang->configure('SIZE',3);

        //create combobox for ranking
        $cobRank = new control\combobox('cobRank');
        $cobRank->configure('LABEL',_('Rank'));
        $cobRank->configure('SOURCE',[0,1,2,3,4,5,6,7,8,9,10]);
        $cobRank->configure('SIZE',3);

		$btnDoForum = new control\button('btnDoForum');
        $btnDoForum->configure('LABEL',_('Add forum'));
        $btnDoForum->configure('TYPE','primary');
        $btnDoForum->p_onclick_plugin = 'forum';
        $btnDoForum->p_onclick_function = 'btnOnclickDoForum';
        
        $btn_cancel = new control\button('btn_cancel');
        $btn_cancel->configure('LABEL',_('Cancel'));
        $btn_cancel->configure('HREF',core\general::createUrl(['service','administrator','load','page','listForums']));
		if(!is_null($forum)){
			$txtName->value = $forum->name;
			$txtDes->value = $forum->des;
			$cobLang->selected_index = $forum->localize;
			$btnDoForum->label = _('Save changes');
			$hidID = new control\hidden('hidID');
			$hidID->value = $forum->id;
			$form->add($hidID);
            $cobRank->configure('SELECTED_INDEX', $forum->rank);
		}
        $row = new control\row;
        $row->configure('IN_TABLE',false);
        $row->add($btnDoForum,2);
        $row->add($btn_cancel,10);
        
        $form->addArray([$txtName,$txtDes,$cobLang,$cobRank,$row]);

        return [_('New forum'),$form->draw()];
    }

    /*
	 * show form for delete forum
     * @param object $forum , forum information
	 * @RETURN html content [title,body]
	 */
    protected function viewSureDeletForum($forum){
        $form = new control\form('frmSureDeletCat');

        $hidID = new control\hidden('hidID');
        $hidID->value = $forum->id;
        $form->add($hidID);

        $label = new control\label(sprintf(_('Are you sure for delete %s'),$forum->name));
        $form->add($label);

        $btnDelete = new control\button('btnDelete');
        $btnDelete->configure('LABEL',_('Yes, Delete'));
        $btnDelete->configure('TYPE','primary');
        $btnDelete->p_onclick_plugin = 'forum';
        $btnDelete->p_onclick_function = 'btnOnclickDeleteforum';

        $btn_cancel = new control\button('btn_cancel');
        $btn_cancel->configure('LABEL',_('Cancel'));
        $btn_cancel->configure('HREF',core\general::createUrl(['service','administrator','load','forum','listForums']));

        $row = new control\row;
        $row->configure('IN_TABLE',false);

        $row->add($btnDelete,1);
        $row->add($btn_cancel,11);
        $form->add($row);

        return [sprintf(_('Delete %s'),$forum->name),$form->draw()];
    }

    /*
	 * Post new topic
     * @param object $forum, forum information
	 * @RETURN html content [title,body]
	 */
    protected function viewNewTopic($forum){
        $form = new control\form('forumFrmNewTopic');

        $hidID = new control\hidden('hidForum');
        $hidID->value = $forum->id;
        $form->add($hidID);

        $txtTitle = new control\textbox('txtTitle');
        $txtTitle->label = _('Title:');

        $txtBody = new control\textarea('txtBody');
        $txtBody->label = _('Body:');

        $btnDelete = new control\button('btnSubmitTopic');
        $btnDelete->configure('LABEL',_('Submit'));
        $btnDelete->configure('TYPE','primary');
        $btnDelete->p_onclick_plugin = 'forum';
        $btnDelete->p_onclick_function = 'btnOnclickDoTopic';

        $btn_cancel = new control\button('btn_cancel');
        $btn_cancel->configure('LABEL',_('Cancel'));
        $btn_cancel->configure('HREF',core\general::createUrl(['forum','home']));

        $row = new control\row;
        $row->configure('IN_TABLE',false);

        $row->add($btnDelete,2);
        $row->add($btn_cancel,10);

        $form->addArray([$txtTitle,$txtBody,$row]);
        return [sprintf(_('New topic in %s'),$forum->name),$form->draw()];
    }
    /*
    * show form for edite topic
     * @param object $topic, topic information
     * @param object $forum, forum information
    * @RETURN html content [title,body]
    */
    protected function viewEditeTopic($forum,$topic){

        $form = new control\form('forumFrmNewTopic');

        $hidID = new control\hidden('hidForum');
        $hidID->value = $forum->id;
        $form->add($hidID);

        $hidTopic = new control\hidden('hidTopic');
        $hidTopic->value = $topic->id;
        $form->add($hidTopic);

        $txtTitle = new control\textbox('txtTitle');
        $txtTitle->value = $topic->title;
        $txtTitle->label = _('Title:');

        $txtBody = new control\textarea('txtBody');
        $txtBody->value = $topic->body;
        $txtBody->label = _('Body:');

        $btnEdite = new control\button('btnEditeTopic');
        $btnEdite->configure('LABEL',_('Edite'));
        $btnEdite->configure('TYPE','primary');
        $btnEdite->p_onclick_plugin = 'forum';
        $btnEdite->p_onclick_function = 'btnOnclickUpdateTopic';

        $btn_cancel = new control\button('btn_cancel');
        $btn_cancel->configure('LABEL',_('Cancel'));
        $btn_cancel->configure('HREF',core\general::createUrl(['forum','showTopic',$topic->id]));

        $row = new control\row;
        $row->configure('IN_TABLE',false);

        $row->add($btnEdite,2);
        $row->add($btn_cancel,10);

        $form->addArray([$txtTitle,$txtBody,$row]);
        return [sprintf(_('New topic in %s'),$forum->name),$form->draw()];
    }
    /*
	 * show main page of forum
     * @param array $forumsInfo, forums information and last topics
	 * @RETURN html content [title,body]
	 */
    protected function viewMain($forumsInfo){
        $form = new control\form('blog_list_posts');

        $table = new control\table('blog_list_posts');
        $counter = 0;
        foreach($forumsInfo as $key=>$objInfo){
            $forum = $objInfo->forumInfo;
            //create table for forum
            $table = new control\table('tblForim');
            $lblForumUrl = new control\button('lbl');
            $lblForumUrl->configure('LABEL', $forum->name);
            $lblForumUrl->configure('TYPE', 'link');
            $lblForumUrl->configure('HREF', core\general::createUrl(['forum', 'showForum', $forum->id]));

            $lblforumDes = new control\button('lbl');
            $lblforumDes->configure('LABEL', $forum->des);
            $lblforumDes->configure('TYPE', 'link');
            $lblforumDes->configure('HREF', core\general::createUrl(['forum', 'showForum', $forum->id]));

            $btnHtml = '';
            if($this->isLogedin()){
                $btnNewTopic = new control\button('lblNewTopic');
                $btnNewTopic->label = _('New topic');
                $btnNewTopic->href = core\general::createUrl(['forum', 'newTopic', $forum->id]);
                $btnNewTopic->type = 'success';
                $btnHtml = $btnNewTopic->draw();
            }


            $table->configure('HEADERS',[$lblForumUrl->draw() . ":" . $lblforumDes->draw() ,$btnHtml]);
            $table->configure('HEADERS_WIDTH',[11,1]);
            $table->configure('ALIGN_CENTER',[false,true]);
            $table->configure('BORDER',false);
            $table->configure('SIZE',12);
            if(!is_null($objInfo->lastTopics)) {
                $orm = db\orm::singleton();
                foreach ($objInfo->lastTopics as $key=>$topic) {
                    $row = new control\row('forum_topic_row');

                    $btnTopic = new control\button('lbl');
                    $btnTopic->configure('LABEL', $topic->title);
                    $btnTopic->configure('TYPE', 'link');
                    $btnTopic->configure('HREF', core\general::createUrl(['forum', 'showTopic', $topic->id]));
                    $topicIcon = new control\tile('topicTile');
                    $topicIcon->add('<span class="glyphicon glyphicon-comment" aria-hidden="true"></span>' . $btnTopic->draw() . ' ' . sprintf(_('( %s Replays )'),$orm->count('forums_replays','topic=?',[$topic->id])));
                    $row->add($topicIcon,10);

                    $lblAuthor = new control\label(sprintf(_('By %s'),$topic->username));
                    $row->add($lblAuthor,1);

                    $table->add_row($row);
                }

            }
            $form->add($table);
        }
        $tleStatic = new control\tile();
        $tleStatic->add(browser\page::showBlock(_('Forum statics'),sprintf(_('%s replays in %s Topics by %s Members. Latest Member: %s'),$this->replaysCount(),$this->topicsCount(),$this->usersCount(),$this->lastRegisteredUser()),'BLOCK','primary'));
        $form->add($tleStatic);
        return [_('Forums'),$form->draw()];
    }

    /*
   * show topic
   * @param object $forum,forum information
   * @param object $topic, topic information
   * @param array $replays, replays on topic
   * @param object $user, current user information
   * @RETURN html content [title,body]
   */
    protected function viewShowTopic($forum,$topic,$replays,$user)
    {
        $calendar = \Mega\Cls\Calendar\Calendar::singleton();
        $avatarHtml = '';
        if ($this->fileExists($topic->photo)) {
            $avatar = new control\image('imgAvatar');
            $avatar->src = $this->getFileAddress($topic->photo);
            $avatar->size = 3;
            $avatar->type = 'img-circle';
            $avatar->style = "width:64px;";
            $avatarHtml = $avatar->draw();
        }
        $label = new control\label($avatarHtml . sprintf(_('By %s in %s'), $topic->username, $calendar->cdate('Y/m/d H:i:s', $topic->publishDate)));
        $topicHtml = new control\tile('topic');
        //show breadcrumb
        $bcAddress = new control\breadcrumb();
        $bcAddress->add(core\general::createUrl(['forum','main']),_('Forums'));
        $bcAddress->add(core\general::createUrl(['forum','showForum',$forum->id]),$forum->name);
        $bcAddress->add(null,_($topic->title));
        $topicHtml->add($bcAddress->draw());

        $row = new control\row;
        $topicBody = new control\tile;
        if(!is_null($user))
            if($topic->username == $user->username){
                $btnEdite = new control\button('btnEditeTopic');
                $btnEdite->label = _('Edite');
                $btnEdite->size = 'xs';
                $btnEdite->href = core\general::createUrl(['forum','editeTopic',$topic->id]);
                $row->add($btnEdite,1);
                $btnDelete = new control\button('btnDeleteTopic');
                $btnDelete->label = _('Delete');
                $btnDelete->href = core\general::createUrl(['forum','sureDeleteTopic',$topic->id]);
                $btnDelete->type = 'danger';
                $btnDelete->size = 'xs';
                $row->add($btnDelete,1);
            }
        $topicBody->add($topic->body);
        $topicBody->add($row->draw());
        $topicHtml->add(browser\page::showBlock($label->draw(), $topicBody->draw(), 'BLOCK', 'primary'));
        if (!is_null($replays)) {
            $topicHtml->add('<h1>' . _('Replays:') . '</h1>');
            foreach ($replays as $replay) {
                $calendar = \core\cls\calendar\calendar::singleton();
                $avatarHtml = '';
                if ($this->fileExists($replay->photo)) {
                    $avatar = new control\image('imgAvatar');
                    $avatar->src = $this->getFileAddress($replay->photo);
                    $avatar->size = 3;
                    $avatar->type = 'img-circle';
                    $avatar->style = "width:45px;";
                    $avatarHtml = $avatar->draw();
                }
                $label = new control\label($avatarHtml . sprintf(_('By %s in %s'), $replay->username, $calendar->cdate('Y/m/d H:i:s', $replay->publishDate)));
                $row = new control\row;
                $replayBody = new control\tile;
                if(!is_null($user))
                    if($replay->username == $user->username){
                        $btnEdite = new control\button('btnEditeReplay');
                        $btnEdite->label = _('Edite');
                        $btnEdite->size = 'xs';
                        $btnEdite->href = core\general::createUrl(['forum','editeReplay',$replay->id]);
                        $row->add($btnEdite,1);
                        $btnDelete = new control\button('btnDeleteReplay');
                        $btnDelete->label = _('Delete');
                        $btnDelete->href = core\general::createUrl(['forum','sureDeleteReplay',$replay->id]);
                        $btnDelete->type = 'danger';
                        $btnDelete->size = 'xs';
                        $row->add($btnDelete,1);
                    }
                $replayBody->add($replay->body);
                $replayBody->add($row->draw());
                $topicHtml->add(browser\page::showBlock($label->draw(), $replayBody->draw(), 'BLOCK', 'default'));
            }
        }
        //add replay option
        if($this->isLogedin())
            $topicHtml->add($this->viewRepayForm($topic));
        else
            $topicHtml->add($this->viewLoginReq());

        return [$topic->title,$topicHtml->draw()];
    }

    /*
   * show form for delete topic
   * @param object $topic, topic information
   * @param array $replays, replays on topic
   * @RETURN html content [title,body]
   */
    public function viewSureDeleteTopic($forum,$topic){
        $form = new control\form('forumsSureDeleteTopic');
        $hidID = new control\hidden('hidID');
        $hidID->value = $topic->id;
        $form->add($hidID);
        $label = new control\label(sprintf(_('Are you sure fore delete %s in %s forum?'),$topic->title,$forum->name));
        $lblWarrning = new control\label(_('This operation can not to be undo and this topic and all replays will be removed!'));
        $lblWarrning->type = 'danger';
        $form->add($lblWarrning);
        $form->add_spc();
        $label->bs_control = true;
        $form->add($label);
        $btnDelete = new control\button('btnDeleteTopic');
        $btnDelete->configure('LABEL',_('Yes,Delete'));
        $btnDelete->configure('TYPE','primary');
        $btnDelete->p_onclick_plugin = 'forum';
        $btnDelete->p_onclick_function = 'btnOnclickDeleteTopic';

        $btn_cancel = new control\button('btn_cancel');
        $btn_cancel->configure('LABEL',_('No,cancell'));
        $btn_cancel->configure('HREF',core\general::createUrl(['forum','showTopic',$topic->id]));

        $row = new control\row;
        $row->configure('IN_TABLE',false);

        $row->add($btnDelete,2);
        $row->add($btn_cancel,10);

        $form->add($row);
        return [sprintf(_('Delete %s'),$topic->title),$form->draw()];
    }

    /*
     * show form for delete replay
     * @param object $topic, topic information
     * @RETURN html content [title,body]
     */
    protected function viewSureDeleteReplay($topic,$replay){
        $form = new control\form('forumsSureDeleteTopic');
        $hidID = new control\hidden('hidID');
        $hidID->value = $replay->id;
        $form->add($hidID);

        $hidTopic = new control\hidden('hidTopic');
        $hidTopic->value = $topic->id;
        $form->add($hidTopic);

        $label = new control\label(sprintf(_('Are you sure fore delete your replay in %s?'),$topic->title));
        $lblWarrning = new control\label(_('This operation can not to be undo and this replay will be removed!'));
        $lblWarrning->type = 'danger';
        $form->add($lblWarrning);
        $form->add_spc();
        $label->bs_control = true;
        $form->add($label);
        $btnDelete = new control\button('btnDeleteTopic');
        $btnDelete->configure('LABEL',_('Yes,Delete'));
        $btnDelete->configure('TYPE','primary');
        $btnDelete->p_onclick_plugin = 'forum';
        $btnDelete->p_onclick_function = 'btnOnclickDeleteReplay';

        $btn_cancel = new control\button('btn_cancel');
        $btn_cancel->configure('LABEL',_('No,cancell'));
        $btn_cancel->configure('HREF',core\general::createUrl(['forum','showTopic',$topic->id]));

        $row = new control\row;
        $row->configure('IN_TABLE',false);

        $row->add($btnDelete,2);
        $row->add($btn_cancel,10);

        $form->add($row);
        return [sprintf(_('Delete Replay in %s topic'),$topic->title),$form->draw()];
    }

    /*
     * return back to replay to topic
     * $param object $topic, topic information
     * @return string, html content
     */
    protected function viewRepayForm($topic){
        $form = new control\form('fromForumReplayTopic');

        $hidID = new control\hidden('hidID');
        $hidID->value = $topic->id;
        $form->add($hidID);

        $txtBody = new control\textarea('txtBody');
        $txtBody->label = sprintf(_('Replay to %s'),$topic->title);
        $form->add($txtBody);

        $btnReplay = new control\button('btnReplay');
        $btnReplay->configure('LABEL',_('Submit Replay'));
        $btnReplay->configure('TYPE','primary');
        $btnReplay->p_onclick_plugin = 'forum';
        $btnReplay->p_onclick_function = 'btnOnclickSubmitReplay';
        $form->add($btnReplay);

        return $form->draw();
    }

    /*
     * show message for login or signup message
     * return string,html content
     */
	protected function viewLoginReq(){
        $btnLogin = new control\button('btnLogin');
        $btnLogin->label = _('Login');
        $btnLogin->type = 'link';
        $btnLogin->href = core\general::createUrl(['users','login']);

        $btnRegister = new control\button('$btnRegister');
        $btnRegister->label = _('Register');
        $btnRegister->type = 'link';
        $btnRegister->href = core\general::createUrl(['users','register']);

        $label = new control\label('<div class="well well-sm">' . sprintf(_('For submit new replay %s or %s'),$btnLogin->draw(),$btnRegister->draw()) . '</div>');
        return $label->draw();
    }

    /*
    * show forum topics
    * @param object $forum, forum information
    * $param object  $topic, topic information
    * @RETURN array html content [title,body]
    */
    protected function viewShowForum($forum,$topics){
        $orm = db\orm::singleton();
        $forumHtml = new control\tile();

        //show breadcrumb
        $bcAddress = new control\breadcrumb();
        $bcAddress->add(core\general::createUrl(['forum','main']),_('Forums'));
        $bcAddress->add(null,$forum->name);
        $forumHtml->add($bcAddress->draw());

        //create table for forum
        $table = new control\table('tblForim');
        $lblForumUrl = new control\button('lbl');
        $lblForumUrl->configure('LABEL', $forum->name);
        $lblForumUrl->configure('TYPE', 'link');
        $lblForumUrl->configure('HREF', core\general::createUrl(['forum', 'showForum', $forum->id]));
        $btnHtml = '';
        if($this->isLogedin()){
            $btnNewTopic = new control\button('lblNewTopic');
            $btnNewTopic->label = _('New topic');
            $btnNewTopic->href = core\general::createUrl(['forum', 'newTopic', $forum->id]);
            $btnNewTopic->type = 'success';
            $btnHtml = $btnNewTopic->draw();
        }
        $table->configure('HEADERS',[$lblForumUrl->draw(),$btnHtml]);
        $table->configure('HEADERS_WIDTH',[11,1]);
        $table->configure('ALIGN_CENTER',[false,true]);
        $table->configure('BORDER',false);
        $table->configure('SIZE',12);
		
		if(!is_null($topics))
			foreach($topics as $key=>$topic){
				$row = new control\row('forum_topic_row');

				$btnTopic = new control\button('lbl');
				$btnTopic->configure('LABEL', $topic->title);
				$btnTopic->configure('TYPE', 'link');
				$btnTopic->configure('HREF', core\general::createUrl(['forum', 'showTopic', $topic->id]));
				$topicIcon = new control\tile('topicTile');
				$topicIcon->add('<span class="glyphicon glyphicon-comment" aria-hidden="true"></span>' . $btnTopic->draw() . ' ' . sprintf(_('( %s Replays )'),$orm->count('forums_replays','topic=?',[$topic->id])));
				$row->add($topicIcon,10);

				$lblAuthor = new control\label(sprintf(_('By %s'),$topic->username));
				$row->add($lblAuthor,1);
				$table->add_row($row);
			}
        $forumHtml->add($table->draw());
        return [_('Forums'),$forumHtml->draw()];
    }

    /*
     * EDITE REPLAY
     * @param object $replay, replay information
     * @RETURN html content [title,body]
     */
    protected function viewEditeReplay($replay){
       $form = new control\form('forumFrmEditeReplay');

        $hidID = new control\hidden('hidID');
        $hidID->value = $replay->id;
        $form->add($hidID);

        $txtBody = new control\textarea('txtBody');
        $txtBody->label = _('Body:');
        $txtBody->value = $replay->body;
        $form->add($txtBody);

        $btnEdite = new control\button('btnEditeReplay');
        $btnEdite->configure('LABEL',_('Edite'));
        $btnEdite->configure('TYPE','primary');
        $btnEdite->p_onclick_plugin = 'forum';
        $btnEdite->p_onclick_function = 'btnOnclickUpdateReplay';

        $btn_cancel = new control\button('btn_cancel');
        $btn_cancel->configure('LABEL',_('Cancel'));
        $btn_cancel->configure('HREF',core\general::createUrl(['forum','showTopic',$replay->topic]));

        $row = new control\row;
        $row->configure('IN_TABLE',false);

        $row->add($btnEdite,2);
        $row->add($btn_cancel,10);
        $form->add($row);
        return [_('Edite replay'),$form->draw()];
    }

    /*
    * show last replays in forum
     * @param array $replays, replays information
    * @return array [title,content]
    */
    protected function viewLastReplays($replays){

        $rreplays = [];
        foreach($replays as $replay){
            array_push($rreplays, ['label' => sprintf(_('%s in %s'),$replay->username,$replay->title) ,'url' => core\general::createUrl(array('forum','showTopic',$replay->id)	)]);
        }
        return [_('Last replays in forum'),$this->createMenu($rreplays,0,FALSE)];
    }

    /*
     * show settings page
     * @param object $settings, plugin information
     * @RETURN html content [title,body]
     */
    protected function viewSettings($settings){
        $form = new control\form('forumsSettings');

        $cobPostNumInHome = new control\combobox('postNumInHome');
        $cobPostNumInHome->configure('LABEL',_('Number of topics in homepage:'));
        $cobPostNumInHome->help = _('With this option you can select number of topics that you want to show in forum home page.');
        $cobPostNumInHome->configure('SELECTED_INDEX',$settings->postNumInHome);
        $cobPostNumInHome->configure('SOURCE',[3,4,5,6,7,8,9,10,15,20,25,30,40,50,100]);
        $cobPostNumInHome->configure('SIZE',3);
        $form->add($cobPostNumInHome);

        $btnEdite = new control\button('btnSaveSettings');
        $btnEdite->configure('LABEL',_('Save'));
        $btnEdite->configure('TYPE','primary');
        $btnEdite->p_onclick_plugin = 'forum';
        $btnEdite->p_onclick_function = 'btnOnclickSaveSettings';

        $btn_cancel = new control\button('btn_cancel');
        $btn_cancel->configure('LABEL',_('Cancel'));
        $btn_cancel->configure('HREF',core\general::createUrl(['service','administrator','load','administrator','dashboard']));

        $row = new control\row;
        $row->configure('IN_TABLE',false);

        $row->add($btnEdite,1);
        $row->add($btn_cancel,11);
        $form->add($row);
        return [_('Forums settings'),$form->draw()];
    }

    /**
     * show last topic that created
     * @param array $topics, topics information
     * @return array [title,content]
     */
    public function viewLastTopics($topics){
        $ttopics = [];
        foreach($topics as $topic){
            $ttopics [] = ['label' => $topic->title ,'url' => core\general::createUrl(array('forum','showTopic',$topic->id)	)];
        }
        return [_('Last Topics'),$this->createMenu($ttopics,0,FALSE)];
    }
}
