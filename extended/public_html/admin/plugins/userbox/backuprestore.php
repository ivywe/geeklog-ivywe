<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | BACKUP & RESTORE
// +---------------------------------------------------------------------------+
// $Id: backuprestore.php
// public_html/admin/plugins/userbox/backuprestore.php
// 20120327 tsuchitani AT ivywe DOT co DOT jp


// @@@@@追加予定：データのバックアップリストア
// @@@@@追加予定：データのクリア
// @@@@@追加予定：サンプルデータのインポート

define ('THIS_SCRIPT', 'backuprestore.php');
//define ('THIS_SCRIPT', 'test.php');

require_once('userbox_functions.php');
require_once ($_CONF['path'] . 'plugins/userbox/lib/lib_configuration.php');

require_once( $_CONF['path_system'] . 'lib-admin.php' );

function fncDisply(
	$pi_name
)
// +---------------------------------------------------------------------------+
// | 画面表示
// | 書式 fncDisply($pi_name)
// +---------------------------------------------------------------------------+
// | 戻値 nomal:編集画面
// +---------------------------------------------------------------------------+
//
{
    global $_CONF;
    global $LANG_USERBOX_ADMIN;

    $tmplfld=DATABOX_templatePath('admin','default',$pi_name);
    $templates = new Template($tmplfld);
    $templates->set_file (array (
        'list' => 'backuprestore.thtml',
    ));

    $templates->set_var('about_thispage', $LANG_USERBOX_ADMIN['about_admin_backuprestore']);
    $templates->set_var ('site_admin_url', $_CONF['site_admin_url']);

    $token = SEC_createToken();
    $retval .= SEC_getTokenExpiryNotice($token);
    $templates->set_var('gltoken_name', CSRF_TOKEN);
    $templates->set_var('gltoken', $token);
    $templates->set_var ( 'xhtml', XHTML );


    $templates->set_var ( 'config', $LANG_USERBOX_ADMIN['config']);
	
	$templates->set_var ( 'config_backup', $LANG_USERBOX_ADMIN['config_backup']);
    $templates->set_var ( 'config_init', $LANG_USERBOX_ADMIN['config_init']);
	$templates->set_var ( 'config_restore', $LANG_USERBOX_ADMIN['config_restore']);
    $templates->set_var ( 'config_update', $LANG_USERBOX_ADMIN['config_update']);
	
    $templates->set_var ( 'config_backup_help', $LANG_USERBOX_ADMIN['config_backup_help']);
    $templates->set_var ( 'config_init_help', $LANG_USERBOX_ADMIN['config_init_help']);
    $templates->set_var ( 'config_restore_help', $LANG_USERBOX_ADMIN['config_restore_help']);
	$templates->set_var ( 'config_update_help', $LANG_USERBOX_ADMIN['config_update_help']);


    $err_backup_file= "";
    if (file_exists($_CONF["path_data"]."userboxconfig_bak.php")) {
        $templates->set_var ('restore_disable', "");
        if (is_writable($_CONF["path_data"]."userboxconfig_bak.php")) {
        }else{
            $err_backup_file= $LANG_USERBOX_ADMIN['err_backup_file_non_writable'];
        }

    }else{
        $templates->set_var ('restore_disabled', "disabled");
        $err_backup_file= $LANG_USERBOX_ADMIN['err_backup_file_not_exist'];
    }
    $templates->set_var ('err_backup_file', $err_backup_file);

    $templates->parse ('output', 'list');

    $content = $templates->finish ($templates->get_var ('output'));
    $retval .=$content;

    return $retval ;

}

function fncMenu(
)
// +---------------------------------------------------------------------------+
// | 機能  menu表示  
// | 書式 fncMenu()
// +---------------------------------------------------------------------------+
// | 戻値 menu 
// +---------------------------------------------------------------------------+
{

    global $_CONF;
    global $LANG_ADMIN;

    global $LANG_USERBOX_ADMIN;

    $retval = '';
    //
    $menu_arr[]=array('url' => $_CONF['site_admin_url'],'text' => $LANG_ADMIN['admin_home']);

    $retval .= ADMIN_createMenu(
        $menu_arr,
        $LANG_USERBOX_ADMIN['instructions'],
        plugin_geticon_userbox()
    );
    
    return $retval;
}


// +---------------------------------------------------------------------------+
// | MAIN                                                                      |
// +---------------------------------------------------------------------------+
//############################
$pi_name    = 'userbox';
//############################
$action ="";
if (isset ($_REQUEST['action'])) {
    $action = COM_applyFilter($_REQUEST['action'],false);
}

if ($action=="" ) {
}else{
    if (!SEC_checkToken()){
        COM_accessLog("User {$_USER['username']} tried to illegally and failed CSRF checks.");
        echo COM_refresh($_CONF['site_admin_url'] . '/index.php');
        exit;
    }
}


$display = '';
$menuno=6;
$information = array();

$information['pagetitle']=$LANG_USERBOX_ADMIN['piname']."backup and restore";
$display.=ppNavbarjp($navbarMenu,$LANG_USERBOX_admin_menu[$menuno]);

if (isset ($_REQUEST['msg'])) {
    $display .= COM_showMessage (COM_applyFilter ($_REQUEST['msg'],
                                                  true), $pi_name);
}

switch ($action) {
    case $LANG_USERBOX_ADMIN['config_init']:
        $dummy=LIB_Deleteconfig($pi_name,$config);
        $dummy=LIB_Initializeconfig($pi_name);
		echo COM_refresh($_CONF['site_admin_url'] . '/plugins/userbox/backuprestore.php');
		exit;
        break;
    case $LANG_USERBOX_ADMIN['config_backup']:
        $display.=LIB_Backupconfig($pi_name);
        break;
    case $LANG_USERBOX_ADMIN['config_restore'];
        $display.=LIB_Restoreconfig($pi_name,$config);
		break;
    case $LANG_USERBOX_ADMIN['config_update']:
		$dummy=LIB_Backupconfig($pi_name,"update");
		$dummy=LIB_Deleteconfig($pi_name,$config);
		$dummy=LIB_Initializeconfig($pi_name);
		$dummy=LIB_Restoreconfig($pi_name,$config,"update");
		echo COM_refresh($_CONF['site_admin_url'] . '/plugins/userbox/backuprestore.php');
		exit;
        break;
 
    default:
}
$display .= fncMenu($pi_name);
$display.=fncDisply($pi_name);

$display=COM_startBlock($LANG_USERBOX_ADMIN['piname'],''
         ,COM_getBlockTemplate('_admin_block', 'header'))
         .$display
         .COM_endBlock(COM_getBlockTemplate('_admin_block', 'footer'));

$display=DATABOX_displaypage($pi_name,'_admin',$display,$information);
COM_output($display);


?>
