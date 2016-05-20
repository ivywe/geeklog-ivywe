<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | fieldset.php Maintenance
// +---------------------------------------------------------------------------+
// $Id: fieldset.php
// 20120509 tsuchitani AT ivywe DOT co DOT jp

define ('THIS_SCRIPT', 'userbox/fieldset.php');
//define ('THIS_SCRIPT', 'userbox/fieldset.php');

require_once('userbox_functions.php');
require_once ($_CONF['path'] . 'plugins/userbox/lib/lib_fieldset.php');
require_once( $_CONF['path_system'] . 'lib-admin.php' );

// +---------------------------------------------------------------------------+
// | MAIN                                                                      |
// +---------------------------------------------------------------------------+
//############################
$pi_name    = 'userbox';
//############################

// 引数
$action = '';
if (isset ($_REQUEST['action'])) {
    $action = COM_applyFilter ($_REQUEST['action'], false);
}
if (isset ($_REQUEST['mode'])) {
    $mode = COM_applyFilter ($_REQUEST['mode'], false);
}
$msg = '';
if (isset ($_REQUEST['msg'])) {
    $msg = COM_applyFilter ($_REQUEST['msg'], true);
}
$id = '';
if (isset ($_REQUEST['id'])) {
    $id = COM_applyFilter ($_REQUEST['id'], true);
}

$old_mode="";
if (isset($_REQUEST['old_mode'])) {
    $old_mode = COM_applyFilter($_REQUEST['old_mode'],false);
    if ($mode==$LANG_ADMIN['cancel']) {
        $mode = $old_mode;
    }
}

if (($mode == $LANG_ADMIN['save']) && !empty ($LANG_ADMIN['save'])) { // save
    $mode="save";
}else if (($mode == $LANG_ADMIN['delete']) && !empty ($LANG_ADMIN['delete'])) {
    $mode="delete";
}
if ($action == $LANG_ADMIN['cancel'])  { // cancel
    $mode="";
}

//echo "mode=".$mode."<br>";
if ($mode=="" OR $mode=="edit" OR $mode=="new" OR $mode=="drafton" OR $mode=="draftoff"
	OR $mode=="export" OR $mode=="import" OR $mode=="copy"
	OR $mode=="listfields" OR $mode=="editfields" 
	OR $mode=="listgroups" OR $mode=="editgroups" 
	) {
}else{
    if (!SEC_checkToken()){
 //    if (SEC_checkToken()){//テスト用
        COM_accessLog("User {$_USER['username']} tried to illegally and failed CSRF checks.");
        echo COM_refresh($_CONF['site_admin_url'] . '/index.php');
        exit;
    }
}


if ($mode=="exportexec") {
    LIB_export ($pi_name);
    exit;
}

//
$menuno=31;
$display = '';
$information = array();

switch ($mode) {
    case 'export':
        $information['pagetitle']=$LANG_USERBOX_ADMIN['piname'];
        $display .= DATABOX_Confirmation($pi_name,$mode);
        break;
    case 'new':// 新規登録
        $information['pagetitle']=$LANG_USERBOX_ADMIN['piname'].$LANG_USERBOX_ADMIN['new'];
        $display .= LIB_Edit($pi_name,"", $edt_flg,$msg);
        break;

    case 'save':// 保存
		$retval= LIB_Save ($pi_name,$edt_flg,$navbarMenu,$menuno);
        $information['pagetitle']=$retval['title'];
		$display.=$retval['display'];

        break;
    case 'delete':// 削除
        $display .= LIB_delete($pi_name);
		break;
    case 'copy'://コピー
    case 'edit':// 編集
        if (!empty ($id) ) {
            $information['pagetitle']=$LANG_USERBOX_ADMIN['piname'].$LANG_USERBOX_ADMIN['edit'];
            $display .= LIB_Edit($pi_name,$id, $edt_flg,$msg,"",$mode);
        }
        break;

    case 'import':
        $information['pagetitle']=$LANG_USERBOX_ADMIN['piname'].$LANG_USERBOX_ADMIN['import'];
        $display .= LIB_import($pi_name);
        break;
	case 'listfields':
		$information['pagetitle']=$LANG_USERBOX_ADMIN['piname'];
        $display .= LIB_ListFields($pi_name,$id);
        break;
	case 'editfields':
        $information['pagetitle']=$LANG_USERBOX_ADMIN['piname'].$LANG_USERBOX_ADMIN['new'];
        $display .= LIB_EditFields($pi_name,$id);
        break;
    case 'savefieldsetfields':// fields 保存
        $display .= LIB_savefields ($pi_name,$id);
        break;
	
	case 'listgroups':
		$information['pagetitle']=$LANG_USERBOX_ADMIN['piname'];
        $display .= LIB_ListGroups($pi_name,$id);
        break;
	case 'editgroups':// edit groups
        $information['pagetitle']=$LANG_USERBOX_ADMIN['piname'].$LANG_USERBOX_ADMIN['new'];
        $display .= LIB_EditGroups($pi_name,$id);
		break;
	case 'savefieldsetgroups':// fields 保存
        $display .= LIB_savegroups ($pi_name,$id);
        break;

    default:// 初期表示、一覧表示
        $information['pagetitle']=$LANG_USERBOX_ADMIN['piname'];
        if (isset ($msg)) {
            $display .= COM_showMessage ($msg,$pi_name);
        }
        $display .= LIB_List($pi_name);
}
$display =COM_startBlock($LANG_USERBOX_ADMIN['piname'],''
            ,COM_getBlockTemplate('_admin_block', 'header'))
         .ppNavbarjp($navbarMenu,$LANG_USERBOX_admin_menu[$menuno])
         .LIB_Menu($pi_name)
         .$display
         .COM_endBlock(COM_getBlockTemplate('_admin_block', 'footer'));
$display=DATABOX_displaypage($pi_name,'_admin',$display,$information);

COM_output($display);

?>
