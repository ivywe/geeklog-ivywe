<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// |  download
// +---------------------------------------------------------------------------+
// $Id: public_html/databox/download.php
define ('THIS_SCRIPT', 'databox/download.php');
//define ('THIS_SCRIPT', 'databox/test.php');

//20150220 tsuchitani AT ivywe DOT co DOT jp http://www.ivywe.co.jp/

require_once ('../lib-common.php');
if (!in_array('databox', $_PLUGINS)) {
    COM_handle404();
    exit;
}

//debug 時 true
$_DATABOX_VERBOSE = false;

// +---------------------------------------------------------------------------+
// MAIN
// +---------------------------------------------------------------------------+
//############################
$pi_name    = 'databox';
//############################
//
$display = '';
$page_title=$LANG_DATABOX_ADMIN['piname'];
//ログイン要否チェック
if (COM_isAnonUser()){
    if  ($_CONF['loginrequired']
            OR ($_DATABOX_CONF['loginrequired'] == 3)
            OR ($_DATABOX_CONF['loginrequired'] == 2 AND $id>0) ){
        $display .= DATABOX_siteHeader($pi_name,'',$page_title);
        $display .= SEC_loginRequiredForm();
        $display .= DATABOX_siteFooter($pi_name);
        COM_output($display);
        exit;
    }
}


//引数
//public_html/download.php?id=1&field_id=
//public_html/download.php?id=1&code=
$id = '';
if (isset ($_REQUEST['id'])) {
    $id = COM_applyFilter ($_REQUEST['id'], true);
}
$code = '';
if (isset ($_REQUEST['code'])) {
    $code = COM_applyFilter ($_REQUEST['code'], false);
}
$field_id = '';
if (isset ($_REQUEST['field_id'])) {
    $field_id = COM_applyFilter ($_REQUEST['field_id'], false);
}
$field_code = '';
if (isset ($_REQUEST['field_code'])) {
    $field_code = COM_applyFilter ($_REQUEST['field_code'], false);
}
$mode = '';
if (isset ($_REQUEST['mode'])) {
    $mode = COM_applyFilter ($_REQUEST['mode'], false);
}
$rt=DATABOX_fieldownload($pi_name,$id,$code,$field_id,$field_code,$mode);

?>