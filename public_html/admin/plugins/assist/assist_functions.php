<?php
// +---------------------------------------------------------------------------+
// | assist_function 共通＆navbarMenu設定
// +---------------------------------------------------------------------------+
// $Id: assist_function.php
// public_html/admin/plugins/assist/assist_function.php
// 2010/12/17 tsuchitani AT ivywe DOT co DOT jp

define ('THIS_PLUGIN', 'assist');

require_once('../../../lib-common.php');
require_once ($_CONF['path'] . 'plugins/assist/lib/ppNavbar.php');

$edt_flg=FALSE;

// 権限チェック
if (SEC_hasRights('assist.admin')) {
}else{
    $display="";
    $display .= COM_siteHeader('menu', $MESSAGE[30]);
    $display .= COM_startBlock ($MESSAGE[30], '',
                                COM_getBlockTemplate ('_msg_block', 'header'));
    $display .= $MESSAGE[35];
    $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
    $display .= COM_siteFooter();

    // Log attempt to error.log
    COM_accessLog("User {$_USER['username']} tried to illegally access the assist administration screen.");

    echo $display;

    exit;
}

$adminurl=$_CONF['site_admin_url'] .'/plugins/'.THIS_PLUGIN."/";
$navbarMenu = array();
$navbarMenu[$LANG_ASSIST_admin_menu['1']]= $adminurl.'information.php';
$navbarMenu[$LANG_ASSIST_admin_menu['2']]= $adminurl.'user.php';
$navbarMenu[$LANG_ASSIST_admin_menu['3']]= $adminurl.'vars.php';
$navbarMenu[$LANG_ASSIST_admin_menu['4']]= $adminurl.'newsletter.php';
$navbarMenu[$LANG_ASSIST_admin_menu['5']]= $adminurl.'backuprestore.php';


$pro=$_CONF['path'] . 'plugins/assist/proversion/';
if (file_exists($pro)) {
    $navbarMenu[$LANG_ASSIST_admin_menu['8']]= $adminurl.'pro.php';
}

?>