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

        $txtCaseName = new control\textbox('txtCaseName');
        $txtCaseName->label = _t('Case Model');

        $txtMainboard = new control\textbox('txtMainboard');
        $txtMainboard->label = _t('Mainboard Model');

        $cobRam = new control\combobox('cobRamValue');
        $cobRam->label = _t('Ram value (GB)');
        $cobRam->source = [1,2,4,6,8,16,24,32,64];
        $cobRam->selected_index = 4;

        $form->addArray([$txtSerial,$txtCaseName,$txtMainboard,$cobRam]);
        return[_t('Submit new case'),$form->draw()];
    }
}
