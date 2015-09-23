<?php
// +---------------------------------------------------------------------------+
// | userbox_function 共通＆navbarMenu設定
// +---------------------------------------------------------------------------+
// $Id: userbox_function.php
// public_html/userbox/mydata/userbox_function.php
// 20101118 tsuchitani AT ivywe DOT co DOT jp

define ('THIS_PLUGIN', 'userbox');

require_once('../../lib-common.php');
if (!in_array('userbox', $_PLUGINS)) {
    COM_handle404();
    exit;
}

require_once ($_CONF['path'] . 'plugins/userbox/lib/ppNavbar.php');

$edt_flg=FALSE;

// 権限チェック
if  ($_USER['uid']<2 ) {
    $page_title= $LANG_USERBOX['myprofile'];
    $display .= DATABOX_siteHeader('USERBOX','',$page_title);
    $display .= SEC_loginRequiredForm();
    echo $display;
    exit;
}

if (SEC_hasRights('userbox.user') OR ($_USERBOX_CONF['allow_loggedinusers']==1)){
}else{
    $page_title= $LANG_USERBOX['myprofile'];
    $display .= DATABOX_siteHeader('USERBOX','',$page_title);
    $display .= $LANG_USERBOX['nohit'];
    echo $display;
    exit;
}

$url=$_CONF['site_url'] ."/".THIS_PLUGIN."/myprofile/";
$navbarMenu = array();

//profile
$navbarMenu[$LANG_USERBOX_user_menu['1']]= $url.'view.php';

if ($_USERBOX_CONF['allow_profile_update']==1 ){
	$navbarMenu[$LANG_USERBOX_user_menu['2']]= $url.'profile.php';
}else{
	if (SEC_hasRights ('userbox.edit') ){
		$navbarMenu[$LANG_USERBOX_user_menu['2']]= $url.'profile.php';
	}
}

//securitygroup
if ($_USERBOX_CONF['allow_profile_update']==1 AND  $_USERBOX_CONF['allow_group_update']==1){
    $navbarMenu[$LANG_USERBOX_user_menu['7']]= $url.'securitygroup.php';
}else{
	if (SEC_hasRights ('userbox.joingroup')){
		$navbarMenu[$LANG_USERBOX_user_menu['7']]= $url.'securitygroup.php';
	}
}

?>