<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | page.php 個別表示 
// +---------------------------------------------------------------------------+
// $Id: page.php
// public_html/databox/page.php
// 2014/11/25 tsuchitani AT ivywe DOT co DOT jp

define ('THIS_SCRIPT', 'databox/page.php');
//define ('THIS_SCRIPT', 'databox/test.php');
//debug 時 true
$_DATABOX_VERBOSE = false;

require_once('../lib-common.php');
if (!in_array('databox', $_PLUGINS)) {
    COM_handle404();
    exit;
}
//ログイン要否チェック
if (COM_isAnonUser()){
    if  ($_CONF['loginrequired']
            OR ($_DATABOX_CONF['loginrequired'] == 3)
            OR ($_DATABOX_CONF['loginrequired'] == 2)
            OR ($_DATABOX_CONF['loginrequired'] == 1 ) 
			){
		echo $LANG_DATABOX['loginrequired'];
		exit;
    }

}

// +---------------------------------------------------------------------------+
// | MAIN                                                                      |
// +---------------------------------------------------------------------------+
//############################
$pi_name    = 'databox';
//############################
$display = '';

// 引数
//public_html/page.php?code=xxxx&template=yyyy
$url_rewrite = false;
$q = false;
$url = $_SERVER["REQUEST_URI"];
if ($_CONF['url_rewrite']) {
    $q = strpos($url, '?');
    if ($q === false) {
        $url_rewrite = true;
    }elseif (substr($url, $q - 4, 4) != '.php') {
        $url_rewrite = true;
    }
}
//
if ($url_rewrite){
	COM_setArgNames(array('code','template','dummy1','dummy2'));
    $code=COM_applyFilter(COM_getArgument('code'));
    $template=COM_applyFilter(COM_getArgument('template'));
}else{
    $code = COM_applyFilter($_GET['code']);
    $template = COM_applyFilter($_GET['template']);
}

$msg = '';
if (isset ($_GET['msg'])) {
    $msg = COM_applyFilter ($_GET['msg'], true);
}

$display = '';

$information = array();
// 'コメントを追加',
if (isset ($_POST['reply']) && ($_POST['reply'] == $LANG01[25])) {
    $display .= COM_refresh ($_CONF['site_url'] . '/comment.php?sid='
             . $_POST['pid'] . '&pid=' . $_POST['pid']
             . '&type=' . $_POST['type']);
    echo $display;
    exit;
}

$id=0;
$retval= databox_data($id,$template,"yes","page",$code);
$layout=$retval['layout'];
$information['headercode']=$retval['headercode'];
$information['pagetitle']=$retval['title'];
if (isset ($msg)) {
    $display.= COM_showMessage ($msg,$pi_name);
}
$display.=$retval['display'];
$display.= databox_Comment($id,$code);

$display=DATABOX_displaypage($pi_name,$layout,$display,$information);

COM_output($display);

?>
