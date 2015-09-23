<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// |  検索リスト
// +---------------------------------------------------------------------------+
// $Id: public_html/databox/search.php
define ('THIS_SCRIPT', 'databox/search.php');
//define ('THIS_SCRIPT', 'databox/test.php');

define ('NEXT_SCRIPT', 'databox/data.php');
//define ('THIS_SCRIPT', 'databox/test.php');
//20140924 tsuchitani AT ivywe DOT co DOT jp http://www.ivywe.co.jp/

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
//public_html/search.php?tp=1&gr1=1&at71=1&at72=1||2||3&at73=1000_2000
//labo3.itsup.net/gl210/databox/search.php?type=
//&attributeA=100000_15000
//&attributeA=2000|3000
//&attributeA=2000

$display = '';
$information = array();

$argary=databox_searcharg("notautotag",$_REQUEST);
$rt=databox_search("notautotag",$argary);

$information['pagetitle']=$rt['pagetitle'];
$information['headercode']=$rt['headercode'];
$display=$rt['display'];


//---
$display=DATABOX_displaypage($pi_name,'',$display,$information);
COM_output($display);

?>