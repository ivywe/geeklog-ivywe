<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | profile view
// +---------------------------------------------------------------------------+
// $Id: view.php
// public_html/userbox/myprofile/view.php
// 20110627 tsuchitani AT ivywe DOT co DOT jp
// 20200526 hiroron AT hiroron DOT com

define ('THIS_SCRIPT', 'userbox/myprofile/view.php');

include_once('userbox_functions.php');

//ログイン要チェック
if (COM_isAnonUser()) {
    $display = DATABOX_displaypage('userbox', '', SEC_loginRequiredForm(), array('pagetitle' => $LANG_PROFILE[4]));
    COM_output($display);
    exit;
}

if ($_USERBOX_CONF['allow_profile_update']==1 ){
}else{
    if (SEC_hasRights ('userbox.edit') ){
	}else{
		COM_accessLog("User {$_USER['username']} tried to profile and failed ");
		COM_redirect($_CONF['site_url'] . '/index.php');
	}
}

// +---------------------------------------------------------------------------+
// | 機能  プロフィール確認画面表示
// | 書式 fncview ()
// +---------------------------------------------------------------------------+
// | 戻値 nomal:                                                               |
// +---------------------------------------------------------------------------+
function fncview ()
{
    $pi_name="userbox";

	global $_CONF;
    global $LANG_USERBOX_ADMIN;

    //template フォルダ
    $tmplfld=DATABOX_templatePath('myprofile','default',$pi_name);
    $tmpl = new Template($tmplfld);

    $tmpl->set_file (array (
                'view' => 'view.thtml',
            ));

    //--

    //$tmpl->set_var('site_admin_url', $_CONF['site_admin_url']);

	$tmpl->set_var('about_thispage', $LANG_USERBOX_ADMIN['about_myprofile_view']);
	
    $tmpl->parse ('output', 'view');
    $view = $tmpl->finish ($tmpl->get_var ('output'));

	$retval="";
	$retval.=$view;
	$ret= userbox_profile($_USER['uid'],"","","view");
	$retval.= $ret['display'];
	
    return $retval;
}


// +---------------------------------------------------------------------------+
// | MAIN                                                                      |
// +---------------------------------------------------------------------------+
//############################
$pi_name = 'userbox';
//############################
$menuno = 1;

$display = ppNavbarjp($navbarMenu,$LANG_USERBOX_user_menu[$menuno]);
$display .= fncview();
$display = DATABOX_displaypage($pi_name, '', $display, array('pagetitle'=>$LANG_USERBOX_ADMIN['piname']));

COM_output($display);
