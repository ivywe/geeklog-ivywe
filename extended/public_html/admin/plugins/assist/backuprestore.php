<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | BACKUP & RESTORE
// +---------------------------------------------------------------------------+
// $Id: backuprestore.php
// public_html/admin/plugins/assist/backuprestore.php
// 20111108 tsuchitani AT ivywe DOT co DOT jp
// last update 20181102 hiroron AT hiroron DOT com

define ('THIS_SCRIPT', 'assist/backuprestore.php');
//define ('THIS_SCRIPT', 'assist/test.php');

require_once('assist_functions.php');
require_once ($_CONF['path'] . 'plugins/assist/lib/lib_configuration.php');

require_once( $_CONF['path_system'] . 'lib-admin.php' );

//
// +---------------------------------------------------------------------------+
// | 画面表示
// | 書式 fncDisply($pi_name)
// +---------------------------------------------------------------------------+
// | 戻値 nomal:編集画面
// +---------------------------------------------------------------------------+
// 20101201
function fncDisply($pi_name)
{
    global $_CONF;
    global $LANG_ASSIST_ADMIN;

    $tmplfld=assist_templatePath('admin','default',$pi_name);
    $templates = new Template($tmplfld);
    $templates->set_file (array (
        'list' => 'backuprestore.thtml',
    ));

    $templates->set_var('about_thispage', $LANG_ASSIST_ADMIN['about_admin_backuprestore']);
    $templates->set_var ('site_admin_url', $_CONF['site_admin_url']);

    $token = SEC_createToken();
    $retval = '';
    $retval .= SEC_getTokenExpiryNotice($token);
    $templates->set_var('gltoken_name', CSRF_TOKEN);
    $templates->set_var('gltoken', $token);
    $templates->set_var ( 'xhtml', XHTML );

    $templates->set_var('script', THIS_SCRIPT);

    $templates->set_var ( 'config', $LANG_ASSIST_ADMIN['config']);
    $templates->set_var ( 'config_backup', $LANG_ASSIST_ADMIN['config_backup']);
    $templates->set_var ( 'config_init', $LANG_ASSIST_ADMIN['config_init']);
    $templates->set_var ( 'config_restore', $LANG_ASSIST_ADMIN['config_restore']);
    $templates->set_var ( 'config_update', $LANG_ASSIST_ADMIN['config_update']);
	
	$templates->set_var ( 'config_backup_help', $LANG_ASSIST_ADMIN['config_backup_help']);
    $templates->set_var ( 'config_init_help', $LANG_ASSIST_ADMIN['config_init_help']);
    $templates->set_var ( 'config_restore_help', $LANG_ASSIST_ADMIN['config_restore_help']);
	$templates->set_var ( 'config_update_help', $LANG_ASSIST_ADMIN['config_update_help']);

    $err_backup_file= "";
    if (file_exists($_CONF["path_data"]."assistconfig_bak.php")) {
        $templates->set_var ('restore_disable', "");
        if (is_writable($_CONF["path_data"]."assistconfig_bak.php")) {
        }else{
            $err_backup_file= $LANG_ASSIST_ADMIN['err_backup_file_non_writable'];
        }

    }else{
        $templates->set_var ('restore_disabled', "disabled");
        $err_backup_file= $LANG_ASSIST_ADMIN['err_backup_file_not_exist'];
    }
    $templates->set_var ('err_backup_file', $err_backup_file);

    $templates->parse ('output', 'list');

    $content = $templates->finish ($templates->get_var ('output'));
    $retval .=$content;

    return $retval ;

}

// +---------------------------------------------------------------------------+
// | 機能  menu表示  
// | 書式 fncMenu()
// +---------------------------------------------------------------------------+
// | 戻値 menu 
// +---------------------------------------------------------------------------+
function fncMenu(
)
{

    global $_CONF;
    global $LANG_ADMIN;
    global $LANG_ASSIST_ADMIN;

    $retval = '';

    $adminurl=$_CONF['site_admin_url'] .'/plugins/'.THIS_PLUGIN."/";
    //
    $menu_arr = array (
        array('text' => $LANG_ASSIST_ADMIN['piname'], 'url' => $adminurl.'information.php'),
        //
        array('text' => $LANG_ADMIN['admin_home'], 'url' => $_CONF['site_admin_url'])
    );
    $retval .= ADMIN_createMenu(
        $menu_arr,
        $LANG_ASSIST_ADMIN['about_admin_backuprestore'],
        plugin_geticon_assist()
    );
    
    return $retval;
}


// +---------------------------------------------------------------------------+
// | MAIN                                                                      |
// +---------------------------------------------------------------------------+
//############################
$pi_name    = 'assist';
//############################
// 引数
$action='';
$mode='';
if (isset ($_REQUEST['action'])) {
    $action = COM_applyFilter($_REQUEST['action'],false);
}
if (isset ($_REQUEST['mode'])) {
    $mode = COM_applyFilter ($_REQUEST['mode'], false);
}

if ($action == $LANG_ASSIST_ADMIN['config_backup'])  { // configbackup
    $mode='configbackup';
} elseif ($action == $LANG_ASSIST_ADMIN['config_init'])  { // configinit
    $mode='configinit';
} elseif ($action == $LANG_ASSIST_ADMIN['config_restore'])  { // configrestore
    $mode='configrestore';
} elseif ($action == $LANG_ASSIST_ADMIN['config_update'])  { // configupdate
    $mode='configupdate';
} elseif ($action == $LANG_ADMIN['cancel'])  { // cancel
    $mode="";
}

if ($mode=="" 
    OR $mode=="configinit"
    OR $mode=="configbackup"
    OR $mode=="configrestore"
    OR $mode=="configupdate"
    ) {
}else{
    if (!SEC_checkToken()){
        COM_accessLog("User {$_USER['username']} tried to illegally and failed CSRF checks.");
        echo COM_refresh($_CONF['site_admin_url'] . '/index.php');
        exit;
    }
}


$display='';
$menuno=5;
$information = array();
//$information['what']='menu';
//$information['rightblock']=false;
$information['pagetitle']=$LANG_ASSIST_ADMIN['piname']."backup and restore";

//uikit3でnavbarが使えなくなったのでコメントアウト
//$display.=ppNavbarjp($navbarMenu,$LANG_ASSIST_admin_menu[$menuno]);
if (isset ($_REQUEST['msg'])) {
    $display .= COM_showMessage (COM_applyFilter ($_REQUEST['msg'],
                                                  true), $pi_name);
}

switch ($mode) {
    case 'configinitexec':
        $display .= fncMenu();
        $dummy=LIB_Deleteconfig($pi_name,$config);
        $dummy=LIB_Initializeconfig($pi_name);
        $display.="config init";
        $display.=fncDisply($pi_name);
        break;
    case 'configbackupexec':
        $display.=LIB_Backupconfig($pi_name);
        break;
    case 'configrestoreexec';
        $display.=LIB_Restoreconfig($pi_name,$config);
        break;
    case 'configupdateexec':
        $display .= fncMenu();
        $dummy=LIB_Backupconfig($pi_name,"update");
        $dummy=LIB_Deleteconfig($pi_name,$config);
        $dummy=LIB_Initializeconfig($pi_name);
        $dummy=LIB_Restoreconfig($pi_name,$config,"update");
        $display.="config update";
        $display.=fncDisply($pi_name);
	    break;
	case 'configbackup':
	case 'configinit':
	case 'configrestore':
	case 'configupdate':
        $information['pagetitle']=$LANG_ASSIST_ADMIN['piname'];
        $display .= fncMenu();
        $display .= assist_Confirmation($pi_name,$mode);
        break;
    default:
        $display .= fncMenu();
        $display.=fncDisply($pi_name);
}
$display=COM_startBlock($LANG_ASSIST_ADMIN['piname'],''
         ,COM_getBlockTemplate('_admin_block', 'header'))
         .$display
         .COM_endBlock(COM_getBlockTemplate('_admin_block', 'footer'));

$display=assist_displaypage($pi_name,'_admin',$display,$information);
COM_output($display);


?>
