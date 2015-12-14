<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | information.php 更新                                                      |
// +---------------------------------------------------------------------------+
// $Id: information.php
// public_html/admin/plugins/assist/information.php
// 20091204 tsuchi AT geeklog DOT jp
// $Id: information.php
// last update 20111021

define ('THIS_SCRIPT', 'information.php');

include_once('assist_functions.php');

// +---------------------------------------------------------------------------+
// | 機能  表示                                                                |
// | 書式 fncDisplay()                                                         |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:表示                                                           |
// +---------------------------------------------------------------------------+
function fncDisplay()
{
    global $_CONF;
	global $LANG_ASSIST_ADMIN;
    global $_ASSIST_CONF;
	global $_DB_dbms;

    $retval="";

    $pi_name="assist";
    $tmplfld=assist_templatePath('admin','default',$pi_name);
    $T = new Template($tmplfld);
	
	$lang = COM_getLanguageName();
	$path = $_CONF['site_admin_url'].'/plugins/assist/docs/';
	//$path = 'docs/';
	if (!file_exists($path . $lang . '/')) {
		$lang = 'japanese';//'english';
	}
	$document_url = $path . $lang . '/';
	
	$T->set_file ('admin','information.thtml');
	$T->set_var ('pi_name',$pi_name);
	$T->set_var('version',$_ASSIST_CONF['version']);
	
	$T->set_var('dbms',$_DB_dbms);
	$T->set_var('dbversion',DB_getVersion());
	$T->set_var('php_os',PHP_OS);
	$T->set_var('phpversion',phpversion());
	
	if  ($_CONF['facebook_consumer_key']==""){
		$T->set_var('fbid',$LANG_ASSIST_ADMIN['err_fbid']);
	}else{
		$T->set_var('fbid',$_CONF['facebook_consumer_key']);
	}
	$T->set_var('piname', $LANG_ASSIST_ADMIN['piname']);
	$T->set_var('about_thispage', $LANG_ASSIST_ADMIN['about_admin_information']);
	
	$T->set_var('lang_document', $LANG_ASSIST_ADMIN['document']);
	$T->set_var('document_url',$document_url);
	
	$T->set_var('online', $LANG_ASSIST_ADMIN['online']);
	$T->set_var('lang_configuration', $LANG_ASSIST_ADMIN['configuration']);
	$T->set_var('lang_autotags', $LANG_ASSIST_ADMIN['autotags']);
	$T->set_var('lang_templatesetvar', $LANG_ASSIST_ADMIN['templatesetvar']);
	
    $T->set_var('site_url', $_CONF['site_url']);
    $T->set_var('site_admin_url', $_CONF['site_admin_url']);

    $T->parse('output', 'admin');
    $retval.= $T->finish($T->get_var('output'));

    return $retval;
}


// +---------------------------------------------------------------------------+
// | MAIN                                                                      |
// +---------------------------------------------------------------------------+
// 引数

if ($mode=="" OR $mode=="importform" OR $mode=="deleteform") {
}else{
    if (!SEC_checkToken()){
 //    if (SEC_checkToken()){//テスト用
        COM_accessLog("User {$_USER['username']} tried to illegally and failed CSRF checks.");
        echo COM_refresh($_CONF['site_admin_url'] . '/index.php');
        exit;
    }
}
$menuno=1;
$display = '';

$information = array();
$information['what']='menu';
$information['pagetitle']=$LANG_ASSIST_ADMIN['piname'];
$information['rightblock']=false;

if (isset ($_REQUEST['msg'])) {
    $display .= COM_showMessage (COM_applyFilter ($_REQUEST['msg'],
                                                  true), 'assist');
}
$display.=ppNavbarjp($navbarMenu,$LANG_ASSIST_admin_menu[$menuno]);
$display.=fncDisplay();


//FOR GL2.0.0 
if (COM_versionCompare(VERSION, "2.0.0",  '>=')){
	$display = COM_createHTMLDocument($display,$information);
}else{
	$display = COM_siteHeader ($information['what'], $information['pagetitle']).$display;
	$display .= COM_siteFooter($information['rightblock']);
}


COM_output($display);

?>
