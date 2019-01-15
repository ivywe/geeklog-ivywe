<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | view
// +---------------------------------------------------------------------------+
// $Id: view.php
// public_html/admin/plugins/databox/view.php
// 20110627 tsuchitani AT ivywe DOT co DOT jp

define ('THIS_SCRIPT', 'databox/view.php');
//define ('THIS_SCRIPT', 'databox/test.php');

require_once('../../../lib-common.php');

// 権限チェック
if (SEC_hasRights('databox.admin')) {
}else{
    // $display="";

    // Log attempt to error.log
    COM_accessLog("User {$_USER['username']} tried to illegally access the databox administration screen.");

    echo COM_createHTMLDocument($MESSAGE[30], array('what' => $what, 'pagetitle' => $page_title, 'breadcrumbs' => $breadcrumbs, 'headercode' => $headercode, 'rightblock' => $rightblock));

    exit;
}


// +---------------------------------------------------------------------------+
// | 機能  データ確認画面表示
// | 書式 fncview ($id)
// +---------------------------------------------------------------------------+
// | 戻値 nomal:                                                               |
// +---------------------------------------------------------------------------+
function fncview (
	$id
	,$template
)
{
    $pi_name="databox";

	global $_CONF;
    global $LANG_DATABOX_ADMIN;

    //template フォルダ
    $tmplfld=databox_templatePath('admin','default',$pi_name);
    $tmpl = new Template($tmplfld);

    $tmpl->set_file (array (
                'view' => 'view.thtml',
            ));

    //--

    $tmpl->set_var('site_admin_url', $_CONF['site_admin_url']);
	
	if ($template===""){
		$tmpl->set_var('about_thispage', $LANG_DATABOX_ADMIN['about_admin_view']);
	}else{
		$tmpl->set_var('about_thispage', "");
	}
    $tmpl->parse ('output', 'view');
    $view = $tmpl->finish ($tmpl->get_var ('output'));

	$retval="";
	$retval.=$view;
	$ret= databox_data($id,$template,"","view");
	$retval.=$ret['display'];


    return $retval;
}


// +---------------------------------------------------------------------------+
// | MAIN                                                                      |
// +---------------------------------------------------------------------------+
//############################
$pi_name    = 'databox';
//############################

$id = null;
if (isset ($_REQUEST['id'])) {
    $id = COM_applyFilter ($_REQUEST['id'], true);
}
$template = COM_applyFilter($_REQUEST['template']);

$display="";

$page_title=$LANG_DATABOX_ADMIN['piname'];

$display .= COM_createHTMLDocument(fncview($id,$template), array('what' => $what, 'pagetitle' => $page_title, 'breadcrumbs' => $breadcrumbs, 'headercode' => $headercode, 'rightblock' => $rightblock));


echo $display;

?>
