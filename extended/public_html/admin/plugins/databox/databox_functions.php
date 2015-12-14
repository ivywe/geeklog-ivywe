<?php
// +---------------------------------------------------------------------------+
// | databox_function 共通＆navbarMenu設定                                     |
// +---------------------------------------------------------------------------+
// $Id: databox_function.php
// public_html/admin/plugins/databox/databox_function.php
// 20100924 tsuchitani AT ivywe DOT co DOT jp
// 20120509 fieldset add

define ('THIS_PLUGIN', 'databox');

require_once('../../../lib-common.php');
if (!in_array('databox', $_PLUGINS)) {
    COM_handle404();
    exit;
}

require_once ($_CONF['path'] . 'plugins/databox/lib/ppNavbar.php');

$edt_flg=FALSE;

// 権限チェック
if (SEC_hasRights('databox.admin')) {
}else{
	$information = array();
	$information['pagetitle']=$MESSAGE[30];
    $display="";
    $display .= COM_startBlock ($MESSAGE[30], '',
                                COM_getBlockTemplate ('_msg_block', 'header'));
    $display .= $MESSAGE[35];
    $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));

    COM_accessLog("User {$_USER['username']} tried to illegally access the databox administration screen.");

	$display=DATABOX_displaypage($pi_name,'_admin',$display,$information);
	COM_output($display);

    exit;
}

$adminurl=$_CONF['site_admin_url'] .'/plugins/'.THIS_PLUGIN."/";
$navbarMenu = array();
$navbarMenu[$LANG_DATABOX_admin_menu['1']]= $adminurl.'information.php';
$navbarMenu[$LANG_DATABOX_admin_menu['2']]= $adminurl.'data.php';
$navbarMenu[$LANG_DATABOX_admin_menu['3']]= $adminurl.'field.php';
$navbarMenu[$LANG_DATABOX_admin_menu['31']]= $adminurl.'fieldset.php';
$navbarMenu[$LANG_DATABOX_admin_menu['4']]= $adminurl.'category.php';
$navbarMenu[$LANG_DATABOX_admin_menu['5']]= $adminurl.'group.php';
$navbarMenu[$LANG_DATABOX_admin_menu['51']]= $adminurl.'mst.php';
$navbarMenu[$LANG_DATABOX_admin_menu['6']]= $adminurl.'backuprestore.php';

//
$xml=$_CONF['path'] . 'plugins/databox/xml/';
if (file_exists($xml)) {
    $navbarMenu[$LANG_DATABOX_admin_menu['8']]= $adminurl.'xml.php';
}

$csv=$_CONF['path'] . 'plugins/databox/csv/';
if (file_exists($csv)) {
    $navbarMenu[$LANG_DATABOX_admin_menu['9']]= $adminurl.'csv.php';
}

$maps=$_CONF['path'] . 'plugins/databox/maps/';
if (file_exists($maps)) {
    if (in_array("maps", $_PLUGINS)){
        $navbarMenu[$LANG_DATABOX_admin_menu['10']]= $adminurl.'maps.php';
    }
}

$csv=$_CONF['path'] . 'plugins/databox/sel/';
if (file_exists($csv)) {
    $navbarMenu[$LANG_DATABOX_admin_menu['11']]= $adminurl.'sel.php';
}

?>