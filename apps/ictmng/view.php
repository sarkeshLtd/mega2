<?php
namespace apps\ictmng;
use Mega\control as control;
use Mega\cls\core as core;
use Mega\cls\Database as db;
use Mega\cls\browser as browser;

class view {
    use addons;

    /*
     * show dashboard page
     * $PARAM ARRAY $user USERINFO
     * @RETURN STR HTML CONTENT
     */
    public function viewDashboard($user){

        $form = new control\form('frmDashboard');
        $lblName = new control\label(sprintf(_t("Welcome %s !"),$user->username));
        $form->add($lblName);
        $btnNewCase = new control\button('btnNewCase');
        $btnNewCase->label = _t('Submit New Case');
        $btnNewCase->href = core\general::createUrl(['ictmng','submitnewcase']);

        $form->add($btnNewCase);
        return [_t('Dashboard'),$form->draw()];
    }

    /*
	 * submit new PC
     * @PARAM ARRAY $USER , USER INFO
	 * @RETURN STR HTML CONTENT
	 */
    public function viewSubmitNewCase($user){
       $form = new control\form('frmsubmitcase');
        $txtSerial = new control\textbox('txtCaseSerial');
        $txtSerial->label = _t('Serial of case');

        $form->add($txtSerial);
        return[_t('Submit new case'),$form->draw()];
    }
}
