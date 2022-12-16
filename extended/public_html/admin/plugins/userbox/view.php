<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | view
// +---------------------------------------------------------------------------+
// $Id: view.php
// public_html/admin/plugins/userbox/view.php
// 20110622 tsuchitani AT ivywe DOT co DOT jp

define ('THIS_SCRIPT', 'userbox/view.php');
//define ('THIS_SCRIPT', 'userbox/test.php');

require_once('../../../lib-common.php');

$display='';
// 権限チェック
if (SEC_hasRights('userbox.admin')) {
}else{
    $display .= COM_showMessageText($MESSAGE[29], $MESSAGE[30]);
    $display = COM_createHTMLDocument($display, array('pagetitle' => $MESSAGE[30]));
    // Log attempt to error.log
    COM_accessLog("User {$_USER['username']} tried to illegally access the userbox administration screen.");
    COM_output($display);
    exit;
}


// +---------------------------------------------------------------------------+
// | 機能  プロフィール確認画面表示
// | 書式 fncview ($uid)
// +---------------------------------------------------------------------------+
// | 戻値 nomal:                                                               |
// +---------------------------------------------------------------------------+
function fncview (
	$uid
	,$template
)
{
    $pi_name="userbox";

	global $_CONF;
    global $LANG_USERBOX_ADMIN;

    //template フォルダ
    $tmplfld=DATABOX_templatePath('admin','default',$pi_name);
    $tmpl = new Template($tmplfld);

    $tmpl->set_file (array (
                'view' => 'view.thtml',
            ));

    //--

    $tmpl->set_var('site_admin_url', $_CONF['site_admin_url']);
	
	if ($template===""){
		$tmpl->set_var('about_thispage', $LANG_USERBOX_ADMIN['about_admin_view']);
	}else{
		$tmpl->set_var('about_thispage', "");
	}
    $tmpl->parse ('output', 'view');
    $view = $tmpl->finish ($tmpl->get_var ('output'));

    $information = array();
    $retval= userbox_profile($uid,$template,"","view");
    $layout=$retval['layout'];
    $information['headercode']=$retval['headercode'];
    $information['pagetitle']=$retval['title'];
    $display=$view;
    $display.=$retval['display'];
    $display=DATABOX_displaypage($pi_name,$layout,$display,$information);

    COM_output($display);
}


// +---------------------------------------------------------------------------+
// | MAIN                                                                      |
// +---------------------------------------------------------------------------+
//############################
$pi_name    = 'userbox';
//############################

$id = '';
if (isset ($_REQUEST['id'])) {
    $id = COM_applyFilter ($_REQUEST['id'], true);
}
$template = COM_applyFilter($_REQUEST['template']);
if  ($id===""){
	$id=$_USER['uid'];
}

fncview($id,$template);


?>
